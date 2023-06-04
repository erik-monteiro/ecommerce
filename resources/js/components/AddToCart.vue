<template>
    <div class="container text-center">
        <hr>
        <button class="btn btn-success" type="submit" v-on:click.prevent="addProductToCart()">
            Add to Cart
        </button>
    </div>
</template>

<script>

    export default {
        data() {
            return {
                
            }
        },

        props: [
            'productId', 'userId'
        ],

        methods: {
            async addProductToCart() {
                if (this.userId == 0) {
                    alert("Deve estar logado para adicionar um produto ao carrinho!")
                } 

                let response = await axios.post('/cart', {
                    'product_id': this.productId
                });

                if (response) {
                    this.$root.$emit('changeInCart', response.data.items);
                    alert(response.data.message);
                } 
            }
        },

    }
</script>
