<template>
    <div>
        <li class="nav-item active">
            <a class="nav-link bg-warning rounded-pill p-3 text-dark" href="/checkout">Cart</a><small>{{ this.itemCount }}</small>
        </li>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                itemCount: ''
            }
        },

        mounted() {
            this.$root.$on('changeInCart', (item) => {
                this.itemCount = item;
            });
        },

        /*
        methods: {
            getCartItemsOnPageLoad() {
                let response = axios.post('/cart');
                this.itemCount = response.data.items;
                alert(this.itemCount);
            }
        },

        created() {
            return this.getCartItemsOnPageLoad();
        }
        */

    }
</script>