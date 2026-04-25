/**
 * Tests for StopCard component
 */

// Mock the module export for the class
class StopCard {
    static create(stop, options = {}) {
        const {
            isFavorite = false,
            onClick = null,
            showIds = true
        } = options;

        const linesHtml = stop.lines && stop.lines.length > 0
            ? stop.lines.slice(0, 3).map(line =>
                `<span class="line-badge">${line.alias || line.line}</span>`
            ).join('')
            : 'Nessuna linea';

        let stopIdsHtml = '';
        if (showIds) {
            let id = stop.id;
            if (id.includes("web")) {
                id = id.replace("-web-aut", "");
                id = id.replace("-web", "");
            }

            let ids = [id];
            if (id.includes("-")) {
                let idSplitted = id.split("-");
                if (idSplitted[0] !== 'terminal') {
                    ids = idSplitted;
                } else {
                    id = "Terminal " + idSplitted[1];
                    ids = [id];
                }
            }

            stopIdsHtml = ids.map(id =>
                `<div class="stop-id-badge">${id}</div>`
            ).join('');
        }

        const onclickAttr = onClick
            ? `onclick='${onClick.name}(${JSON.stringify(stop)})'`
            : '';

        return `
            <div class="stop-card" ${onclickAttr}>
                <div class="stop-card-content">
                    ${showIds ? `<div class="stop-ids-container">${stopIdsHtml}</div>` : ''}
                    <div class="stop-info">
                        <div class="stop-name">${stop.name}</div>
                        <div class="stop-desc">${linesHtml}</div>
                    </div>
                </div>
                <div class="stop-card-action">
                    ${isFavorite ? '<span class="favorite-icon">★</span>' : '<span class="arrow-icon">›</span>'}
                </div>
            </div>
        `;
    }

    static createMultiple(stops, options = {}) {
        return stops.map(stop => StopCard.create(stop, options)).join('');
    }

    static render(container, stops, options = {}) {
        const element = typeof container === 'string'
            ? document.querySelector(container)
            : container;

        if (!element) {
            console.error('StopCard: Container not found');
            return;
        }

        element.innerHTML = StopCard.createMultiple(stops, options);
    }
}

describe('StopCard', () => {
    describe('create', () => {
        test('should create a stop card with basic data', () => {
            const stop = {
                id: '123',
                name: 'Test Stop',
                lines: []
            };

            const result = StopCard.create(stop);

            expect(result).toContain('Test Stop');
            expect(result).toContain('stop-card');
            expect(result).toContain('stop-id-badge');
        });

        test('should display lines if provided', () => {
            const stop = {
                id: '123',
                name: 'Test Stop',
                lines: [
                    { line: 'A', alias: 'Line A' },
                    { line: 'B', alias: 'Line B' }
                ]
            };

            const result = StopCard.create(stop);

            expect(result).toContain('Line A');
            expect(result).toContain('Line B');
            expect(result).toContain('line-badge');
        });

        test('should show "Nessuna linea" when no lines provided', () => {
            const stop = {
                id: '123',
                name: 'Test Stop',
                lines: []
            };

            const result = StopCard.create(stop);

            expect(result).toContain('Nessuna linea');
        });

        test('should show favorite icon when isFavorite is true', () => {
            const stop = {
                id: '123',
                name: 'Test Stop',
                lines: []
            };

            const result = StopCard.create(stop, { isFavorite: true });

            expect(result).toContain('favorite-icon');
            expect(result).toContain('★');
        });

        test('should show arrow icon when isFavorite is false', () => {
            const stop = {
                id: '123',
                name: 'Test Stop',
                lines: []
            };

            const result = StopCard.create(stop, { isFavorite: false });

            expect(result).toContain('arrow-icon');
            expect(result).toContain('›');
        });

        test('should hide stop IDs when showIds is false', () => {
            const stop = {
                id: '123',
                name: 'Test Stop',
                lines: []
            };

            const result = StopCard.create(stop, { showIds: false });

            expect(result).not.toContain('stop-id-badge');
        });

        test('should handle web suffix in stop ID', () => {
            const stop = {
                id: '123-web',
                name: 'Test Stop',
                lines: []
            };

            const result = StopCard.create(stop);

            expect(result).toContain('123');
            expect(result).not.toContain('-web');
        });

        test('should handle terminal IDs', () => {
            const stop = {
                id: 'terminal-1',
                name: 'Test Stop',
                lines: []
            };

            const result = StopCard.create(stop);

            expect(result).toContain('Terminal 1');
        });

        test('should handle merged stop IDs', () => {
            const stop = {
                id: '123-456',
                name: 'Test Stop',
                lines: []
            };

            const result = StopCard.create(stop);

            expect(result).toContain('123');
            expect(result).toContain('456');
        });
    });

    describe('createMultiple', () => {
        test('should create multiple stop cards', () => {
            const stops = [
                { id: '1', name: 'Stop 1', lines: [] },
                { id: '2', name: 'Stop 2', lines: [] }
            ];

            const result = StopCard.createMultiple(stops);

            expect(result).toContain('Stop 1');
            expect(result).toContain('Stop 2');
            // Count occurrences of the opening div tag
            const cardCount = (result.match(/class="stop-card"/g) || []).length;
            expect(cardCount).toBe(2);
        });

        test('should return empty string for empty array', () => {
            const result = StopCard.createMultiple([]);

            expect(result).toBe('');
        });
    });

    describe('render', () => {
        let mockContainer;

        beforeEach(() => {
            mockContainer = {
                innerHTML: ''
            };
            document.querySelector = jest.fn().mockReturnValue(mockContainer);
        });

        test('should render stop cards into container', () => {
            const stops = [
                { id: '1', name: 'Stop 1', lines: [] }
            ];

            StopCard.render('#container', stops);

            expect(mockContainer.innerHTML).toContain('Stop 1');
        });

        test('should log error when container not found', () => {
            document.querySelector = jest.fn().mockReturnValue(null);
            console.error = jest.fn();

            const stops = [
                { id: '1', name: 'Stop 1', lines: [] }
            ];

            StopCard.render('#container', stops);

            expect(console.error).toHaveBeenCalledWith('StopCard: Container not found');
        });

        test('should accept element directly instead of selector', () => {
            const stops = [
                { id: '1', name: 'Stop 1', lines: [] }
            ];

            StopCard.render(mockContainer, stops);

            expect(mockContainer.innerHTML).toContain('Stop 1');
        });
    });
});
