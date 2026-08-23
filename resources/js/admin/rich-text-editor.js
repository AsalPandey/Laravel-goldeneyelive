import { Jodit } from 'jodit';
import 'jodit/es2021/jodit.min.css';

const editorSelector = 'textarea[data-cms-rich-text]';
const editorInstances = new Map();
const observedForms = new WeakSet();

const toolbarButtons = [
    'paragraph',
    '|',
    'bold',
    'italic',
    'underline',
    '|',
    'ul',
    'ol',
    '|',
    'link',
    'table',
    'goldenEyeImage',
    '|',
    'undo',
    'redo',
];

const isVisible = (element) => element.getClientRects().length > 0;

const restoreSelection = (editor, range) => {
    editor.s.focus();

    if (!range || !editor.editor.contains(range.commonAncestorContainer)) {
        return;
    }

    const selection = window.getSelection();
    selection?.removeAllRanges();
    selection?.addRange(range);
};

const captureSelection = (editor) => {
    const selection = window.getSelection();

    if (!selection?.rangeCount) {
        return null;
    }

    const range = selection.getRangeAt(0);

    return editor.editor.contains(range.commonAncestorContainer)
        ? range.cloneRange()
        : null;
};

const insertManagedImage = (editor, path, range) => {
    const normalizedPath = `/${String(path).replace(/^\/+/, '')}`;
    const image = document.createElement('img');
    image.setAttribute('src', normalizedPath);
    image.setAttribute('alt', '');

    restoreSelection(editor, range);
    editor.s.insertHTML(image.outerHTML);
    editor.synchronizeValues();
};

const editorOptions = {
    buttons: toolbarButtons,
    buttonsMD: toolbarButtons,
    buttonsSM: toolbarButtons,
    buttonsXS: toolbarButtons,
    controls: {
        paragraph: {
            list: {
                p: 'Paragraph',
                h2: 'Heading 2',
                h3: 'Heading 3',
                blockquote: 'Quote',
            },
        },
        goldenEyeImage: {
            icon: 'image',
            tooltip: 'Insert image from Media Vault',
            exec: (editor) => {
                if (typeof window.openMediaVaultForRichText !== 'function') {
                    editor.message.error('The Media Vault is unavailable on this page.');

                    return;
                }

                const range = captureSelection(editor);
                window.openMediaVaultForRichText((path) => insertManagedImage(editor, path, range));
            },
        },
    },
    enter: 'p',
    height: 320,
    toolbarAdaptive: false,
    showCharsCounter: false,
    showWordsCounter: false,
    showXPathInStatusbar: false,
    uploader: {
        insertImageAsBase64URI: false,
        showTabInFileSelector: false,
        url: '',
    },
};

const synchronizeEditor = (editor) => {
    if (!editor.isInDestruct) {
        editor.synchronizeValues();
    }
};

const synchronizeAll = () => {
    editorInstances.forEach(synchronizeEditor);
};

const destroyEditor = (textarea) => {
    const editor = editorInstances.get(textarea);

    if (!editor) {
        return;
    }

    synchronizeEditor(editor);
    editor.destruct();
    editorInstances.delete(textarea);
    delete textarea.dataset.cmsRichTextInitialized;
};

const destroyAll = () => {
    Array.from(editorInstances.keys()).forEach(destroyEditor);
};

const observeForm = (form) => {
    if (!form || observedForms.has(form)) {
        return;
    }

    form.addEventListener('submit', () => {
        editorInstances.forEach((editor, textarea) => {
            if (textarea.form === form) {
                synchronizeEditor(editor);
            }
        });
    });
    observedForms.add(form);
};

const initialize = (scope = document) => {
    const textareas = scope.matches?.(editorSelector)
        ? [scope]
        : Array.from(scope.querySelectorAll?.(editorSelector) ?? []);

    textareas.forEach((textarea) => {
        if (
            editorInstances.has(textarea)
            || textarea.dataset.cmsRichTextInitialized === 'true'
            || !textarea.isConnected
            || !isVisible(textarea)
        ) {
            return;
        }

        const editor = Jodit.make(textarea, editorOptions);
        editor.events.on('change', () => synchronizeEditor(editor));
        editorInstances.set(textarea, editor);
        textarea.dataset.cmsRichTextInitialized = 'true';
        observeForm(textarea.form);
    });
};

const pruneDetachedEditors = () => {
    Array.from(editorInstances.keys())
        .filter((textarea) => !textarea.isConnected)
        .forEach(destroyEditor);
};

const observer = new MutationObserver(() => {
    pruneDetachedEditors();
    initialize(document);
});

window.GoldenEyeRichTextEditor = {
    destroyAll,
    initialize,
    synchronizeAll,
};

document.addEventListener('livewire:navigating', () => {
    synchronizeAll();
    destroyAll();
});
document.addEventListener('livewire:navigated', () => initialize(document));
window.addEventListener('pagehide', synchronizeAll);

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initialize(document);
        observer.observe(document.body, { childList: true, subtree: true });
    }, { once: true });
} else {
    initialize(document);
    observer.observe(document.body, { childList: true, subtree: true });
}
