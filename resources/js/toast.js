/** Short messages at the top of the screen ($store.toast.show('Saved')). */
export function registerToast(Alpine) {
    Alpine.store('toast', {
        items: [],
        show(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => this.dismiss(id), 4000);
        },
        dismiss(id) {
            this.items = this.items.filter((item) => item.id !== id);
        },
    });
}
