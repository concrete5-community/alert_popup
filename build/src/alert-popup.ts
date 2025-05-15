(function() {
'use strict';

const openPopups: Popup[] = [];

function isJQuery(obj: any): obj is JQuery {
  return typeof obj === 'object' && obj !== null && typeof obj.jquery === 'string' && typeof obj.on === 'function';
}

function findDialogElement(query: HTMLElement|string|JQuery): HTMLDialogElement
{
    let el: HTMLElement | null = null;
    if (query instanceof HTMLElement) {
        el = query;
    } else if (typeof query === 'string') {
        query = query.replace(/^\s*#|\s+/, '');
        if (query === '') {
            throw new Error('Empty selector string provided');
        }
        const elements = document.querySelectorAll(`#${query}`);
        switch (elements.length) {
            case 0:
                throw new Error(`No elements found for selector ${query}`);
            case 1:
                el = elements[0] as HTMLElement;
                break;
            default:
                throw new Error(`Multiple elements found for selector ${query}`);
        }
    } else if (isJQuery(query)) {
        switch (query.length) {
            case 0:
                throw new Error("The jQuery object doesn't contain any element");
            case 1:
                el = query[0];
                break;
            default:
                throw new Error('The jQuery object contains more than one elements');
        }
    } else {
        throw new Error('Invalid query type ' + typeof query);
    }
    if (el.tagName !== 'DIALOG') {
        throw new Error('Element is not a dialog, but a ' + el.tagName);
    }
    return el as HTMLDialogElement;
}

function createCloseButton(): HTMLElement
{
    const button = document.createElement('div');
    button.className = 'ccm-alert-popup-close';
    button.style = [
        'position: absolute',
        'top: 0',
        'right: 0',
        'margin: 0',
        'border: none',
        'width: auto',
        'height: auto',
        'line-height: 1',
        'font-size: 13px',
        'text-align: center',
        'text-decoration: none',
        'display: inline-block',
        'vertical-align: top',
        'color: white',
        'background-color: red',
        'font-weight: bold',
        'cursor: pointer',
        'padding: 1px 2px 4px 4px',
    ].join(';');
    button.innerText = '\ud83d\uddd9'; // CANCELLATION X
    return button;
}
class Popup
{
    el: HTMLDialogElement;
    contentEl: HTMLElement;
    clickListener: (e: MouseEvent) => void;
    closeListener: (e: Event) => void;
    closeButton: HTMLElement;
    closed: boolean = false;
    constructor(el: HTMLDialogElement)
    {
        this.el = el;
        const contentEl = el.querySelector('.ccm-alert-popup-content');
        if (!contentEl) {
            throw new Error('No content element found in the dialog');
        }
        this.contentEl = contentEl as HTMLElement;

        this.el.style.position = 'fixed';
        this.el.style.display = 'block';
        this.el.style.padding = '0';
        this.el.style.margin = 'auto';
        const pad = '18px';
        this.contentEl.style.position = 'relative';
        this.contentEl.style.margin = `${pad} 0 0 0`;
        this.contentEl.style.padding = `0 ${pad} ${pad} ${pad}`;
        this.contentEl.style.overflowX = 'visible';
        this.contentEl.style.overflowY = 'auto';

        this.clickListener = (e: MouseEvent) => {
            if (e.target === this.el) {
                this.close();
            }
        };
        this.closeListener = (e: Event) => {
            this.close();
        };
        this.closeButton = createCloseButton();
        this.el.prepend(this.closeButton);
        this.closeButton.addEventListener('click', (e: MouseEvent) => {
            e.stopPropagation();
            e.preventDefault();
            this.close();
        });
        this.el.addEventListener('click', this.clickListener);
        this.el.addEventListener('close', this.closeListener);
        this.el.showModal();
        openPopups.push(this);
    }
    close(): void
    {
        if (this.closed) {
            return;
        }
        this.closed = true;
        this.el.removeEventListener('click', this.clickListener);
        this.el.removeEventListener('close', this.closeListener);
        this.el.removeChild(this.closeButton);
        this.el.style.display = 'none';
        this.el.close();
        const index = openPopups.indexOf(this);
        if (index >= 0) {
            openPopups.splice(index, 1);
        }
    }
}

function showAlertPopup(query: HTMLElement|string|JQuery): void
{
    try {
        const el = findDialogElement(query);
        new Popup(el);
    } catch (e: any) {
        console.warn(e.message || e);
        return;
    }
}

function hideTopmostPopup(): boolean
{
    const popup = openPopups.pop();
    if (!popup) {
        console.warn('No open popups to hide');
        return false;
    }
    popup.close();
    return true;
}

Object.defineProperty(
    window,
    'ccmAlertPopup',
    {
        writable: false,
        value: Object.defineProperties({}, {
            show: {
                value: showAlertPopup,
                writable: false,
                enumerable: true,
                configurable: false,
            },
            hide: {
                value: hideTopmostPopup,
                writable: false,
                enumerable: true,
                configurable: false,
            },
        }),
        enumerable: true,
        configurable: false,
    }
);

})();