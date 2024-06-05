<template>
    <div class="container">
        <div class="invoices">
            <div class="card__header">
                <div>
                    <h2 class="invoice__title">New Invoice</h2>
                </div>
            </div>

            <div class="card__content">
                <div class="card__content--header">
                    <div>
                        <p class="my-1">Customer</p>
                        <select name="" id="" class="input" v-model="customer_id">
                            <option disabled>Select customer</option>
                            <option v-for="customer in allcustomers" :key="customer.id" :value="customer.id">{{ customer.firstname }}</option>
                        </select>
                    </div>
                    <div>
                        <p class="my-1">Date</p>
                        <input id="date" placeholder="dd-mm-yyyy" type="date" class="input" v-model="form.date">
                        <p class="my-1">Due Date</p>
                        <input id="due_date" type="date" class="input" v-model="form.due_date">
                    </div>
                    <div>
                        <p class="my-1">Numero</p>
                        <input type="text" class="input" v-model="form.number">
                        <p class="my-1">Reference(Optional)</p>
                        <input type="text" class="input" v-model="form.reference">
                    </div>
                </div>
                <br><br>
                <div class="table">
                    <div class="table--heading2">
                        <p>Item Description</p>
                        <p>Unit Price</p>
                        <p>Qty</p>
                        <p>Total</p>
                        <p></p>
                    </div>

                    <div class="table--items2" v-for="itemcart in listCart" :key="itemcart.id">
                        <p>#{{ itemcart.item_code }} {{ itemcart.description }}</p>
                        <p>
                            <input type="text" class="input" v-model="itemcart.unit_price">
                        </p>
                        <p>
                            <input type="text" class="input" v-model="itemcart.quantity">
                        </p>
                        <p v-if="itemcart.quantity">
                            $ {{ (itemcart.quantity) * (itemcart.unit_price) }}
                        </p>
                        <p v-else></p>
                        <p style="color: red; font-size: 24px; cursor: pointer;" @click="removeItem(i)">
                            &times;
                        </p>
                    </div>
                    <div style="padding: 10px 30px !important;">
                        <button class="btn btn-sm btn__open--modal" @click="openModel">Add New Line</button>
                    </div>
                </div>

                <div class="table__footer">
                    <div class="document-footer">
                        <p>Terms and Conditions</p>
                        <textarea cols="50" rows="7" class="textarea" v-model="form.terms_and_conditions"></textarea>
                    </div>
                    <div>
                        <div class="table__footer--subtotal">
                            <p>Sub Total</p>
                            <span>$ {{subTotal()}}</span>
                        </div>
                        <div class="table__footer--discount">
                            <p>Discount</p>
                            <input type="text" class="input" v-model="form.discount">
                        </div>
                        <div class="table__footer--total">
                            <p>Grand Total</p>
                            <span>$ {{Total()}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card__header" style="margin-top: 40px;">
                <div>
                    <a class="btn btn-secondary" @click="onSave()">Save</a>
                </div>
            </div>
        </div>

        <div class="modal main__modal" :class="{ show: showModal }">
            <div class="modal__content">
                <span class="modal__close btn__close--modal" @click="closeModal">×</span>
                <h3 class="modal__title">Add Item</h3>
                <hr><br>
                <div class="modal__items">
                    <ul style="list-style: none">
                        <li v-for="(product, i) in listproducts" :key="product.id" style="display: grid; grid-template-columns: 30px 350px 15px; align-items: center; padding-bottom: 5px">
                            <p>{{ i + 1 }}</p>
                            <a href="#">{{ product.item_code }} {{ product.description }}</a>
                            <button @click="addCart(product)" style="border: 1px solid #e0e0e0; width: 35px; height: 35px; cursor: pointer;">+</button>
                        </li>
                    </ul>
                </div>
                <br><hr>
                <div class="model__footer">
                    <button class="btn btn-light mr-2 btn__close--modal" @click="closeModal">Cancel</button>
                    <button class="btn btn-light btn__close--modal" >Save</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter()
let form = ref({
    date: '',
    due_date: '',
    number: '',
    reference: '',
    discount: 0,
    terms_and_conditions: ''
});
let allcustomers = ref([]);
let customer_id = ref(null);
let listCart = ref([]);
const showModal = ref(false);
let listproducts = ref([]);

onMounted(async () => {
    await indexForm();
    await getAllCustomers();
    await getProducts();
});

const indexForm = async () => {
    let response = await axios.get('/api/create_invoice');
    form.value = response.data;
};

const getAllCustomers = async () => {
    let response = await axios.get('/api/customers');
    allcustomers.value = response.data;
};

const getProducts = async () => {
    let response = await axios.get('/api/products');
    listproducts.value = response.data;
};

const addCart = (product) => {
    const itemCart = {
        id: product.id,
        item_code: product.item_code,
        description: product.description,
        unit_price: product.unit_price,
        quantity: 1
    };
    listCart.value.push(itemCart);
    closeModal();
};

const openModel = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};
const removeItem = (i) => {
    listCart.value.splice(i,1)
}
const subTotal = () => {
    let total = 0
    listCart.value.map((data) => {
        total = total + (data.quantity * data.unit_price)
    })
    return total;
}
const Total = () => {
    return subTotal() - form.value.discount
}
const onSave = async () => {
    if (listCart.value.length >= 1) {
        const subtotal = subTotal();
        const grandTotal = Total();

        const formData = {
            invoice_item: listCart.value,
            customer_id: customer_id.value,
            date: form.value.date,
            due_date: form.value.due_date,
            number: form.value.number,
            reference: form.value.reference,
            discount: form.value.discount,
            terms_and_conditions: form.value.terms_and_conditions,
            subtotal: subtotal,
            total: grandTotal,
        };

        try {
            await axios.post('/api/add_invoice', formData);
            listCart.value = [];
            router.push('/');
        } catch (error) {
            console.error("There was an error!", error.response);
        }
    }
}
</script>
