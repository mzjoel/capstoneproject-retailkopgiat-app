import { reactive, computed } from 'vue'

export const cart = reactive({
    items: [],
    
    get count() {
        return this.items.reduce((sum, item) => sum + item.qty, 0)
    },
    
    get subtotal() {
        return this.items.reduce((sum, item) => sum + item.price * item.qty, 0)
    },

    add(product) {
        const existingItem = this.items.find(item => item.id === product.id)
        if (existingItem) {
            existingItem.qty++
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                price: product.raw_price || product.price,
                image: product.image,
                description: product.description,
                subtitle: product.category,
                qty: 1
            })
        }
    },

    remove(id) {
        const index = this.items.findIndex(item => item.id === id)
        if (index !== -1) {
            this.items.splice(index, 1)
        }
    },

    increaseQty(id) {
        const item = this.items.find(item => item.id === id)
        if (item) item.qty++
    },

    decreaseQty(id) {
        const item = this.items.find(item => item.id === id)
        if (item) {
            if (item.qty > 1) {
                item.qty--
            } else {
                this.remove(id)
            }
        }
    }
})
