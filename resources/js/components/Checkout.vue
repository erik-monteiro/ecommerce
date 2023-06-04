<template>
    <div class="container text-dark bg-white p-4 mt-4">
        <div class="row" v-for="item in items" :key="item.id">
            <div class="col">
                <h3>R$ {{ item.sale_price }} </h3>
                <div class="container text-left p-2">
                    <p>{{ item.name }}</p>
                    <p>Quantidade: {{ item.quantity }}</p>
                </div>
            </div> 
            <hr>
        </div>

        <div class="col">
            <div class="container bg-dark text-white" v-for="sumarryItem in items" :key="sumarryItem.id">
                <h3>Sumário</h3>
                <p>Quantidade: {{ sumarryItem.quantity }}</p>
                <p>Preço unitário: R$ {{ sumarryItem.sale_price }}</p>
                <p>Valor total: R$ {{ sumarryItem.total }}</p>
            </div>
        </div>

        <div class="col">
            <h3>Valor total da compra</h3>
            <p>R$ {{ items.totalAmount }}</p>
        </div>
    </div>

    <div class="container text-center text-white bg-dark p-4">
        <h3>Informações pessoais</h3>

        <input type="text" placeholder="nome" class="form-control mb-4" v-model="firstName" name="firstName">
        <input type="text" placeholder="sobrenome" class="form-control mb-4" v-model="lastName" name="lastname">
        <input type="text" placeholder="endereço" class="form-control mb-4" v-model="adress" name="adress">
        <input type="number" placeholder="numero" class="form-control mb-4" v-model="number" name="number"> 
        <input type="text" placeholder="cidade" class="form-control mb-4" v-model="city" name="city">
        <input type="text" placeholder="pais" class="form-control mb-4" v-model="country" name="country">
        <input type="email" placeholder="email" class="form-control mb-4" v-model="email" name="email">
        <input type="number" placeholder="celular" class="form-control mb-4" v-model="phone" name="phone">

        <h3>Informações do pagamento</h3>
        <select class="form-control mb-4" name="cardType" v-model="cardType">
            <option value="">tipo de cartão</option>
            <option value="5">Mastercard</option>
            <option value="6">Visa</option>
            <option value="7">American express</option>
            <option value="8">Discover</option>
        </select>
        <input type="text" placeholder="número do cartão de crédito" class="form-control mb-4" name="cardNumber" v-model="cardNumber">
        <input type="text" placeholder="código cvv" class="form-control mb-4" name="cvv" v-model="cvv">
        <select class="form-control mb-4" name="expirationMonth" v-model="expirationMonth">
            <option value="">mês de expiração</option>
            <option value="01">01</option>
            <option value="02">02</option>
            <option value="03">03</option>
            <option value="04">04</option>
            <option value="05">05</option>
            <option value="06">06</option>
            <option value="07">07</option>
            <option value="08">08</option>
            <option value="09">09</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
        </select>
        <select class="form-control mb-4" name="expirationYear" v-model="expirationYear">
            <option value="">ano de expiração</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2025">2025</option>
            <option value="2026">2026</option>
        </select>

        <button class="btn btn-primary" v-on:click.prevent="getUserAdress()">Clique aqui para comprar</button>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                items: [],
                country: '',
                firstName: '',
                lastName: '',
                adress: '',
                number: '',
                city: '',
                email: '',
                phone: '',

                cardType: '',
                cardNumber: '',
                cvv: '',
                expirationMonth: '',
                expirationYear: '',
            }
        },

        methods: {
            async getCartItems() {
                let response = await axios.get('/checkout/get/items');
                this.items = response.data;

                console.log(this.items);
            },

            async getUserAdress() {
                if (this.firstName != '' && this.adress != '' && this.cardNumber != '' && this.cardType != '') {
                    let response = await axios.post('/process/user/payment', {
                        'country': this.country,
                        'firstName': this.firstName,
                        'lastName': this.lastName,
                        'adress': this.adress,
                        'number': this.number,
                        'city': this.city,
                        'email': this.email,
                        'phone': this.phone,

                        'cardType': this.cardType,
                        'cardNumber': this.cardNumber,
                        'cvv': this.cvv, 
                        'expirationMonth': this.expirationMonth,
                        'expirationYear': this.expirationYear,
                        'amount': this.items.totalAmount,
                        'order': this.items
                    });
                    
                    if (response.data.success) {
                        alert(response.data.success);
                    } else {
                        alert(response.data.error);
                    }

                    setTimeout(() => {
                        window.location.href= '/';
                    }, 1500);
                } else {
                    alert('Complete as informações solicitadas!');
                }
            }
        },
        
        created() {
            this.getCartItems();
        }
    }
</script>