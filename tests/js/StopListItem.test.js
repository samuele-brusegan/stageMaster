/**
 * Tests for StopListItem Web Component
 */

// Register the custom element before tests
if (!customElements.get('stop-list-item')) {
    customElements.define('stop-list-item', class StopListItem extends HTMLElement {
        static watchedList = [
            {
                attribute: "",
                htmlElement: "",
                innerAttribute: "",
            },
        ];
        watchedList;

        constructor() {
            super();
            const shadow = this.attachShadow({mode: 'open'});
            const template = document.createElement('template');
            template.innerHTML = `<HTML>`;
            shadow.appendChild(template.content.cloneNode(true));
        }

        static get observedAttributes() {
            let array = [];
            StopListItem.watchedList.forEach(element => {
                array.push(element.attribute);
            });
            return array;
        }

        attributeChangedCallback(name, oldValue, newValue) {
            StopListItem.watchedList.forEach(element => {
                if (element.attribute === name) {
                    let htmlElement = this.shadowRoot.querySelector(element.htmlElement);
                    if (htmlElement) {
                        if (element.innerAttribute !== "") {
                            let tag = element.innerAttribute;
                            htmlElement[tag] = newValue;
                        } else {
                            htmlElement.textContent = newValue;
                        }
                    }
                }
            });
        }

        connectedCallback() {
        }
    });
}

describe('StopListItem', () => {
    let StopListItem;

    beforeEach(() => {
        // Get the registered class
        StopListItem = customElements.get('stop-list-item');
        // Reset watchedList before each test
        StopListItem.watchedList = [
            {
                attribute: "",
                htmlElement: "",
                innerAttribute: "",
            },
        ];
    });

    describe('constructor', () => {
        test('should create shadow DOM', () => {
            const element = document.createElement('stop-list-item');
            
            expect(element.shadowRoot).toBeDefined();
            expect(element.shadowRoot.mode).toBe('open');
        });
    });

    describe('observedAttributes', () => {
        test('should return empty array when watchedList is empty', () => {
            StopListItem.watchedList = [];
            
            const result = StopListItem.observedAttributes;
            
            expect(result).toEqual([]);
        });

        test('should return array of attributes from watchedList', () => {
            StopListItem.watchedList = [
                { attribute: 'data-name', htmlElement: '.name', innerAttribute: '' },
                { attribute: 'data-value', htmlElement: '.value', innerAttribute: '' }
            ];
            
            const result = StopListItem.observedAttributes;
            
            expect(result).toEqual(['data-name', 'data-value']);
        });
    });

    describe('attributeChangedCallback', () => {
        test('should handle attribute change with textContent', () => {
            const element = document.createElement('stop-list-item');
            StopListItem.watchedList = [
                { attribute: 'data-name', htmlElement: '.name', innerAttribute: '' }
            ];
            
            // Mock the shadowRoot.querySelector
            const mockElement = { textContent: '' };
            element.shadowRoot.querySelector = jest.fn().mockReturnValue(mockElement);
            
            element.attributeChangedCallback('data-name', 'old', 'new');
            
            expect(mockElement.textContent).toBe('new');
        });

        test('should handle attribute change with innerAttribute', () => {
            const element = document.createElement('stop-list-item');
            StopListItem.watchedList = [
                { attribute: 'data-value', htmlElement: '.value', innerAttribute: 'value' }
            ];
            
            const mockElement = { value: '' };
            element.shadowRoot.querySelector = jest.fn().mockReturnValue(mockElement);
            
            element.attributeChangedCallback('data-value', 'old', 'new');
            
            expect(mockElement.value).toBe('new');
        });

        test('should do nothing when attribute not in watchedList', () => {
            const element = document.createElement('stop-list-item');
            StopListItem.watchedList = [
                { attribute: 'data-name', htmlElement: '.name', innerAttribute: '' }
            ];
            
            const mockElement = { textContent: '' };
            element.shadowRoot.querySelector = jest.fn().mockReturnValue(mockElement);
            
            element.attributeChangedCallback('data-other', 'old', 'new');
            
            expect(mockElement.textContent).toBe('');
        });

        test('should handle when htmlElement is not found', () => {
            const element = document.createElement('stop-list-item');
            StopListItem.watchedList = [
                { attribute: 'data-name', htmlElement: '.name', innerAttribute: '' }
            ];
            
            element.shadowRoot.querySelector = jest.fn().mockReturnValue(null);
            
            // Should not throw error
            expect(() => {
                element.attributeChangedCallback('data-name', 'old', 'new');
            }).not.toThrow();
        });
    });

    describe('connectedCallback', () => {
        test('should exist', () => {
            const element = document.createElement('stop-list-item');
            
            expect(typeof element.connectedCallback).toBe('function');
        });
    });
});
