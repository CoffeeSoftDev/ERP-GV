class Productos extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Productos";
    }

    render() {
        this.layout();
    }

    layout() {
        this.primaryLayout({
            parent: 'root',
            id: this.PROJECT_NAME,
            class: 'w-full min-h-screen',
            card: {
                filterBar: { class: 'w-full border-b border-gray-700 pb-4', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-4', id: `container${this.PROJECT_NAME}` }
            }
        });

        $(`#${this.PROJECT_NAME}`).prepend(`
            <div class="px-6 pt-6 pb-4">
                <h1 class="text-3xl font-bold text-white">🛍️ Gestión de Productos</h1>
                <p class="text-gray-400 mt-2">Administración de productos y servicios del catálogo</p>
            </div>
        `);

        $(`#container${this.PROJECT_NAME}`).html(`
            <div class="px-6">
                <div class="bg-gray-800 rounded-xl p-6 text-center">
                    <i class="fas fa-box text-6xl text-purple-500 mb-4"></i>
                    <h2 class="text-2xl font-semibold text-white mb-2">Módulo de Productos en Construcción</h2>
                    <p class="text-gray-400">La funcionalidad completa se implementará en las siguientes tareas</p>
                </div>
            </div>
        `);
    }
}
