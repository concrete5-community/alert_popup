(function() {
'use strict';

if ((window as any).ccmAlertPopup?.show) {
    console.warn('ccmAlertPopup is already defined');
    return;
}

const openPopups: Popup[] = [];

function isJQuery(obj: any): obj is JQuery {
  return typeof obj === 'object' && obj !== null && typeof obj.jquery === 'string' && typeof obj.on === 'function';
}

function cssTimeToMillisecs(cssTime: string): number|null {
    const match = cssTime.match(/^(\d+(\.(\d+))?)\s*(ms|s)$/);
    if (!match) {
        return null;
    }
    const value = parseFloat(match[1]);
    const unit = match[4];
    if (unit === 'ms') {
        return value;
    } else if (unit === 's') {
        return value * 1000;
    }
    return null;
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
    button.innerText = '\ud83d\uddd9'; // CANCELLATION X
    return button;
}

function createStyleElement(el: HTMLDialogElement, animated: boolean): HTMLStyleElement|null
{
    const id = el.id;
    const ruleLines: string[] = [];
    const backdropRules: string[] = [];
    if (el.dataset?.backdropColor) {
        backdropRules.push(`background-color: ${el.dataset.backdropColor};`);
    }
    if (animated) {
        const elStyle = window.getComputedStyle(el);
        const transitionDuration = cssTimeToMillisecs(elStyle.transitionDuration);
        if (transitionDuration !== null) {
            backdropRules.push(`transition-duration: ${transitionDuration}ms;`);
        }
    }
    if (backdropRules.length > 0) {
        ruleLines.push(`dialog#${id}::backdrop { ${backdropRules.join(' ')} }`);
    }
    if (ruleLines.length === 0) {
        return null;
    }
    const style = document.createElement('style');
    style.type = 'text/css';
    style.innerHTML = ruleLines.join('\n');
    (document.head || document.body).appendChild(style);
    return style;
}

interface Options {
    closed?: () => void;
};

class Popup
{
    el: HTMLDialogElement;
    animated: boolean;
    clickListener: (e: MouseEvent) => void;
    cancelListener: (e: Event) => void;
    closeListener: (e: Event) => void;
    closeButton: HTMLElement;
    closed: boolean = false;
    revertTransitionProperty: string|undefined;
    options?: Options;
    styleElement: HTMLStyleElement|null;
    constructor(el: HTMLDialogElement, options?: Options)
    {
        this.el = el;
        this.options = options;
        this.animated = false;
        this.el.classList.forEach((className) => {
            if (className.startsWith('ccm-alert-popup-anim-')) {
                this.animated = true;
            }
        });
        this.clickListener = (e: MouseEvent) => {
            if (e.target === this.el) {
                this.close();
            }
        };
        this.closeListener = (e: Event) => {
            this.close();
        };
        this.cancelListener = (e: Event) => {
            e.preventDefault();
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
        this.el.addEventListener('cancel', this.cancelListener);
        openPopups.push(this);
        this.styleElement = createStyleElement(this.el, this.animated);
        this.el.showModal();
        if (this.animated) {
            window.requestAnimationFrame(() => {
                this.el.classList.add('ccm-alert-popup-open');
                const elStyle = window.getComputedStyle(this.el);
                const transitionDuration = cssTimeToMillisecs(elStyle.transitionDuration);
                if (transitionDuration === null) {
                    console.warn('Invalid transition duration');
                } else {
                    this.revertTransitionProperty = elStyle.transitionProperty;
                    setTimeout(
                        () => this.el.style.transitionProperty = 'none',
                        transitionDuration + 100
                    );
                }
            });
        } else {
            this.el.classList.add('ccm-alert-popup-open');
        }
    }
    close(): void
    {
        if (this.closed) {
            return;
        }
        this.closed = true;
        if (this.revertTransitionProperty !== undefined) {
            this.el.style.transitionProperty = this.revertTransitionProperty;
        }
        this.el.removeEventListener('click', this.clickListener);
        this.el.removeEventListener('close', this.closeListener);
        const dispose = () => {
            this.el.removeEventListener('cancel', this.cancelListener);
            this.el.close();
            this.el.removeChild(this.closeButton);
            const index = openPopups.indexOf(this);
            if (index >= 0) {
                openPopups.splice(index, 1);
            }
            if (this.styleElement) {
                this.styleElement.parentElement!.removeChild(this.styleElement);
            }
            this.options?.closed?.();
        };
        this.el.classList.remove('ccm-alert-popup-open');
        if (this.animated) {
            this.el.addEventListener('transitionend', () => dispose(), { once: true });
        } else {
            dispose();
        }
    }
}

function showAlertPopup(query: HTMLElement|string|JQuery, options?: Options): void
{
    try {
        const el = findDialogElement(query);
        if (openPopups.some(popup => popup.el === el)) {
            throw new Error('Popup is already open');
        }
        new Popup(el, options);
    } catch (e: any) {
        console.warn(e?.message || e || 'Unknown error');
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
            isOpen: {
                get: () => openPopups.length > 0,
                enumerable: true,
                configurable: false,
            },
        }),
        enumerable: true,
        configurable: false,
    }
);

})();