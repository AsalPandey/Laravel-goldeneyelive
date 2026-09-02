[CmdletBinding()]
param (
    [Parameter(Mandatory = $true)]
    [string] $ExpectedRepositoryRoot,

    [Parameter(Mandatory = $true)]
    [string] $ApprovedDatabaseSourcePath,

    [ValidateRange(1024, 65535)]
    [int] $Port = 18217
)

$ErrorActionPreference = 'Stop'
$approvedHash = 'b4c15825c6ec130ecd4d83f73647b43dde72d9fd27d4743c10aca3a9d460a313'
$repositoryRoot = [System.IO.Path]::GetFullPath((Resolve-Path -LiteralPath $ExpectedRepositoryRoot).Path)
$approvedSource = [System.IO.Path]::GetFullPath((Resolve-Path -LiteralPath $ApprovedDatabaseSourcePath).Path)
$harness = Join-Path $repositoryRoot 'tests\run-disposable-browser-acceptance.ps1'
$powerShell = (Get-Process -Id $PID).Path
$temporaryRoot = Join-Path ([System.IO.Path]::GetTempPath()) ('goldeneye-browser-guard-' + [guid]::NewGuid().ToString('N'))

if ((Get-FileHash -LiteralPath $approvedSource -Algorithm SHA256).Hash.ToLowerInvariant() -ne $approvedHash) {
    throw 'Approved regression source does not match the required B4 hash.'
}

New-Item -ItemType Directory -Path $temporaryRoot | Out-Null

try {
    $protectedCopy = Join-Path $temporaryRoot 'protected-approved.sqlite'
    $disposableDatabase = Join-Path $temporaryRoot 'acceptance-disposable.sqlite'
    $incorrectProtectedDatabase = Join-Path $temporaryRoot 'protected-incorrect.sqlite'
    $phpSentinel = Join-Path $temporaryRoot 'php-invoked.txt'
    $fakePhp = Join-Path $temporaryRoot 'php.cmd'

    Copy-Item -LiteralPath $approvedSource -Destination $protectedCopy
    Copy-Item -LiteralPath $approvedSource -Destination $disposableDatabase

    $approvedOutput = @(
        & $powerShell -NoProfile -NonInteractive -ExecutionPolicy Bypass -File $harness `
            -ExpectedRepositoryRoot $repositoryRoot `
            -DisposableDatabasePath $disposableDatabase `
            -ProtectedDatabasePath $protectedCopy `
            -Port $Port `
            -PreflightOnly
    )

    if ($LASTEXITCODE -ne 0) {
        throw "Approved B4 preflight failed: $($approvedOutput -join [Environment]::NewLine)"
    }

    $approvedResult = ($approvedOutput -join [Environment]::NewLine) | ConvertFrom-Json
    if ($approvedResult.protected_hash_before -ne $approvedHash `
        -or $approvedResult.protected_hash_after_preflight -ne $approvedHash) {
        throw 'Approved B4 preflight did not report the required before/after hashes.'
    }

    [System.IO.File]::WriteAllText($incorrectProtectedDatabase, 'incorrect protected database fixture')
    Set-Content -LiteralPath $fakePhp -Encoding ascii -Value @(
        '@echo off'
        "echo invoked>`"$phpSentinel`""
        'exit /b 99'
    )

    $disposableHashBefore = (Get-FileHash -LiteralPath $disposableDatabase -Algorithm SHA256).Hash.ToLowerInvariant()
    $artifactNamesBefore = @(Get-ChildItem -LiteralPath $temporaryRoot -Filter "acceptance-server-$Port*" | Select-Object -ExpandProperty Name)
    $originalPath = $env:PATH

    try {
        $env:PATH = $temporaryRoot + [System.IO.Path]::PathSeparator + $originalPath
        $rejectedOutput = @(
            & $powerShell -NoProfile -NonInteractive -ExecutionPolicy Bypass -File $harness `
                -ExpectedRepositoryRoot $repositoryRoot `
                -DisposableDatabasePath $disposableDatabase `
                -ProtectedDatabasePath $incorrectProtectedDatabase `
                -Port $Port 2>&1
        )
        $rejectedExitCode = $LASTEXITCODE
    }
    finally {
        $env:PATH = $originalPath
    }

    if ($rejectedExitCode -eq 0) {
        throw 'Incorrect starting hash was not rejected.'
    }

    $rejectedMessage = $rejectedOutput -join [Environment]::NewLine
    if ($rejectedMessage -notmatch 'approved B4 hash' -or $rejectedMessage -notmatch 'Startup refused') {
        throw "Incorrect starting hash failed for an unexpected reason: $($rejectedOutput -join [Environment]::NewLine)"
    }

    if (Test-Path -LiteralPath $phpSentinel) {
        throw 'PHP was invoked before the incorrect starting hash was rejected.'
    }

    $disposableHashAfter = (Get-FileHash -LiteralPath $disposableDatabase -Algorithm SHA256).Hash.ToLowerInvariant()
    $artifactNamesAfter = @(Get-ChildItem -LiteralPath $temporaryRoot -Filter "acceptance-server-$Port*" | Select-Object -ExpandProperty Name)
    $listener = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue

    if ($disposableHashAfter -ne $disposableHashBefore) {
        throw 'Disposable database changed during rejected startup.'
    }

    if (Compare-Object -ReferenceObject $artifactNamesBefore -DifferenceObject $artifactNamesAfter) {
        throw 'Rejected startup created acceptance server artifacts.'
    }

    if ($null -ne $listener) {
        throw "Rejected startup left a listener on port $Port."
    }

    $global:LASTEXITCODE = 0

    [ordered]@{
        approved_hash_permits_preflight = $true
        incorrect_hash_rejected_before_php = $true
        incorrect_hash_created_no_server_artifacts = $true
        incorrect_hash_left_no_port_listener = $true
        disposable_database_unchanged_on_rejection = $true
    } | ConvertTo-Json
}
finally {
    $resolvedTemporaryRoot = [System.IO.Path]::GetFullPath($temporaryRoot)
    $resolvedSystemTemp = [System.IO.Path]::GetFullPath([System.IO.Path]::GetTempPath()).TrimEnd('\', '/')

    if ($resolvedTemporaryRoot.StartsWith(
        $resolvedSystemTemp + [System.IO.Path]::DirectorySeparatorChar,
        [System.StringComparison]::OrdinalIgnoreCase
    ) -and (Test-Path -LiteralPath $resolvedTemporaryRoot)) {
        Remove-Item -LiteralPath $resolvedTemporaryRoot -Recurse -Force
    }
}
