[CmdletBinding()]
param (
    [Parameter(Mandatory = $true)]
    [string] $ExpectedRepositoryRoot,

    [Parameter(Mandatory = $true)]
    [string] $DisposableDatabasePath,

    [Parameter(Mandatory = $true)]
    [string] $ProtectedDatabasePath,

    [ValidateRange(1024, 65535)]
    [int] $Port = 18197,

    [switch] $PreflightOnly
)

$ErrorActionPreference = 'Stop'
$approvedProtectedDatabaseHash = 'b4c15825c6ec130ecd4d83f73647b43dde72d9fd27d4743c10aca3a9d460a313'

function Resolve-ExistingPath {
    param (
        [Parameter(Mandatory = $true)]
        [string] $Path
    )

    return [System.IO.Path]::GetFullPath((Resolve-Path -LiteralPath $Path).Path)
}

function Test-PathWithin {
    param (
        [Parameter(Mandatory = $true)]
        [string] $Candidate,

        [Parameter(Mandatory = $true)]
        [string] $Root
    )

    $normalizedCandidate = [System.IO.Path]::GetFullPath($Candidate)
    $normalizedRoot = [System.IO.Path]::GetFullPath($Root).TrimEnd('\', '/')

    return $normalizedCandidate.Equals($normalizedRoot, [System.StringComparison]::OrdinalIgnoreCase) `
        -or $normalizedCandidate.StartsWith(
            $normalizedRoot + [System.IO.Path]::DirectorySeparatorChar,
            [System.StringComparison]::OrdinalIgnoreCase
        )
}

$repositoryRoot = Resolve-ExistingPath (Join-Path $PSScriptRoot '..')
$expectedRoot = Resolve-ExistingPath $ExpectedRepositoryRoot
$gitRoot = [System.IO.Path]::GetFullPath((& git -C $repositoryRoot rev-parse --show-toplevel).Trim())

if ($LASTEXITCODE -ne 0) {
    throw 'Unable to resolve the Git worktree containing this acceptance harness.'
}

if (-not $repositoryRoot.Equals($expectedRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
    throw "Harness repository root '$repositoryRoot' does not equal the explicitly expected root '$expectedRoot'."
}

if (-not $repositoryRoot.Equals($gitRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
    throw "Harness repository root '$repositoryRoot' does not equal Git root '$gitRoot'."
}

$protectedDatabase = Resolve-ExistingPath $ProtectedDatabasePath
$protectedHashBefore = (Get-FileHash -LiteralPath $protectedDatabase -Algorithm SHA256).Hash.ToLowerInvariant()

if ($protectedHashBefore -ne $approvedProtectedDatabaseHash) {
    throw "Protected database starting hash '$protectedHashBefore' does not match the approved B4 hash '$approvedProtectedDatabaseHash'. Startup refused."
}

$disposableDatabase = Resolve-ExistingPath $DisposableDatabasePath

if ($disposableDatabase.Equals($protectedDatabase, [System.StringComparison]::OrdinalIgnoreCase)) {
    throw 'Disposable database resolves to the protected operational database. Startup refused.'
}

$worktreeRoots = @(
    & git -C $repositoryRoot worktree list --porcelain |
        Where-Object { $_ -like 'worktree *' } |
        ForEach-Object { [System.IO.Path]::GetFullPath($_.Substring(9)) }
)

foreach ($worktreeRoot in $worktreeRoots) {
    if (Test-PathWithin -Candidate $disposableDatabase -Root $worktreeRoot) {
        throw "Disposable database '$disposableDatabase' is inside Git worktree '$worktreeRoot'. Startup refused."
    }
}

$php = Get-Command php -ErrorAction Stop
$environmentNames = @(
    'APP_ENV',
    'APP_DEBUG',
    'APP_KEY',
    'APP_URL',
    'DB_CONNECTION',
    'DB_DATABASE',
    'DB_URL',
    'CACHE_STORE',
    'SESSION_DRIVER',
    'QUEUE_CONNECTION',
    'MAIL_MAILER',
    'RECAPTCHA_BYPASS'
)
$originalEnvironment = @{}

foreach ($name in $environmentNames) {
    $originalEnvironment[$name] = [System.Environment]::GetEnvironmentVariable($name, 'Process')
}

try {
    $env:APP_ENV = 'local'
    $env:APP_DEBUG = 'false'
    $env:APP_KEY = 'base64:znAKkADueRoAlhvYlRLFnToy+UUet6RgB1ttxe+1NX0='
    $env:APP_URL = "http://127.0.0.1:$Port"
    $env:DB_CONNECTION = 'sqlite'
    $env:DB_DATABASE = $disposableDatabase
    $env:DB_URL = ''
    $env:CACHE_STORE = 'array'
    $env:SESSION_DRIVER = 'cookie'
    $env:QUEUE_CONNECTION = 'sync'
    $env:MAIL_MAILER = 'array'
    $env:RECAPTCHA_BYPASS = 'false'

    $preflightCode = @'
$root = getcwd();
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$connection = (string) config('database.default');
$database = (string) config('database.connections.'.$connection.'.database');
echo json_encode([
    'cwd' => $root,
    'base_path' => base_path(),
    'app_environment' => $app->environment(),
    'db_connection' => $connection,
    'db_database' => $database,
], JSON_THROW_ON_ERROR);
'@

    Push-Location $repositoryRoot
    try {
        $preflightOutput = @(& $php.Source -r $preflightCode)
        if ($LASTEXITCODE -ne 0 -or $preflightOutput.Count -eq 0) {
            throw 'Laravel acceptance preflight failed.'
        }
    }
    finally {
        Pop-Location
    }

    $preflight = $preflightOutput[-1] | ConvertFrom-Json
    $resolvedBasePath = [System.IO.Path]::GetFullPath([string] $preflight.base_path)
    $resolvedDatabase = [System.IO.Path]::GetFullPath([string] $preflight.db_database)

    if (-not $resolvedBasePath.Equals($repositoryRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
        throw "Laravel base path '$resolvedBasePath' does not equal release root '$repositoryRoot'."
    }

    if ([string] $preflight.app_environment -ne 'local') {
        throw "Laravel environment '$($preflight.app_environment)' is not the required local acceptance environment."
    }

    if ([string] $preflight.db_connection -ne 'sqlite') {
        throw "Laravel database connection '$($preflight.db_connection)' is not sqlite."
    }

    if (-not $resolvedDatabase.Equals($disposableDatabase, [System.StringComparison]::OrdinalIgnoreCase)) {
        throw "Laravel resolved database '$resolvedDatabase' does not equal disposable database '$disposableDatabase'."
    }

    if ($resolvedDatabase.Equals($protectedDatabase, [System.StringComparison]::OrdinalIgnoreCase)) {
        throw 'Laravel resolved the protected operational database. Startup refused.'
    }

    $result = [ordered]@{
        repository_root = $repositoryRoot
        process_cwd = [System.IO.Path]::GetFullPath([string] $preflight.cwd)
        laravel_base_path = $resolvedBasePath
        app_environment = [string] $preflight.app_environment
        db_connection = [string] $preflight.db_connection
        resolved_database = $resolvedDatabase
        protected_database = $protectedDatabase
        protected_hash_before = $protectedHashBefore
        php_executable = $php.Source
        port = $Port
        preflight_only = [bool] $PreflightOnly
    }

    if ($PreflightOnly) {
        $protectedHashAfterPreflight = (Get-FileHash -LiteralPath $protectedDatabase -Algorithm SHA256).Hash.ToLowerInvariant()
        if ($protectedHashAfterPreflight -ne $approvedProtectedDatabaseHash) {
            throw 'Protected database does not match the approved B4 hash after guarded preflight.'
        }

        $result.protected_hash_after_preflight = $protectedHashAfterPreflight
        $result | ConvertTo-Json
        return
    }

    $listener = Get-NetTCPConnection -LocalPort $Port -State Listen -ErrorAction SilentlyContinue
    if ($null -ne $listener) {
        throw "TCP port $Port already has a listener. Startup refused."
    }

    $evidenceDirectory = Split-Path -Parent $disposableDatabase
    $standardOutputPath = Join-Path $evidenceDirectory "acceptance-server-$Port.stdout.log"
    $standardErrorPath = Join-Path $evidenceDirectory "acceptance-server-$Port.stderr.log"
    $statePath = Join-Path $evidenceDirectory "acceptance-server-$Port.json"
    $routerPath = Join-Path $repositoryRoot 'vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php'
    $publicPath = Join-Path $repositoryRoot 'public'

    $server = Start-Process `
        -FilePath $php.Source `
        -ArgumentList @('-S', "127.0.0.1:$Port", $routerPath) `
        -WorkingDirectory $publicPath `
        -WindowStyle Hidden `
        -RedirectStandardOutput $standardOutputPath `
        -RedirectStandardError $standardErrorPath `
        -PassThru

    $started = $false
    try {
        for ($attempt = 0; $attempt -lt 20; $attempt++) {
            Start-Sleep -Milliseconds 250

            if ($server.HasExited) {
                throw "Acceptance server exited during startup. See '$standardErrorPath'."
            }

            try {
                $response = Invoke-WebRequest -Uri "http://127.0.0.1:$Port/" -UseBasicParsing -TimeoutSec 2
                if ($response.StatusCode -eq 200) {
                    $started = $true
                    break
                }
            }
            catch {
            }
        }

        if (-not $started) {
            throw 'Acceptance server did not return HTTP 200 during guarded startup.'
        }

        $protectedHashAfterStartup = (Get-FileHash -LiteralPath $protectedDatabase -Algorithm SHA256).Hash.ToLowerInvariant()
        if ($protectedHashAfterStartup -ne $approvedProtectedDatabaseHash) {
            throw 'Protected database does not match the approved B4 hash after guarded startup.'
        }

        $result.server_pid = $server.Id
        $result.server_working_directory = $publicPath
        $result.server_router = $routerPath
        $result.server_url = "http://127.0.0.1:$Port/"
        $result.protected_hash_after_startup = $protectedHashAfterStartup
        $result.stdout_log = $standardOutputPath
        $result.stderr_log = $standardErrorPath
        $result.state_file = $statePath

        $result | ConvertTo-Json | Set-Content -LiteralPath $statePath -Encoding utf8
        $result | ConvertTo-Json
    }
    catch {
        if (-not $server.HasExited) {
            Stop-Process -Id $server.Id -Force
        }

        throw
    }
}
finally {
    foreach ($name in $environmentNames) {
        [System.Environment]::SetEnvironmentVariable($name, $originalEnvironment[$name], 'Process')
    }
}
