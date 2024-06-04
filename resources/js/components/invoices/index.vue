<template>
    <div class="container">

        <div class="invoices">

            <div class="card__header">
                <div>
                    <h2 class="invoice__title">Invoices</h2>
                </div>
                <div>
                    <a class="btn btn-secondary">
                        New Invoice
                    </a>
                </div>
            </div>

            <div class="table card__content">
                <div class="table--filter">
                <span class="table--filter--collapseBtn ">
                    <i class="fas fa-ellipsis-h"></i>
                </span>
                    <div class="table--filter--listWrapper">
                        <ul class="table--filter--list">
                            <li>
                                <p class="table--filter--link table--filter--link--active">
                                    All
                                </p>
                            </li>
                            <li>
                                <p class="table--filter--link ">
                                    Paid
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="table--search">
                    <div class="table--search--wrapper">
                        <select class="table--search--select" name="" id="">
                            <option value="">Filter</option>
                        </select>
                    </div>
                    <div class="relative">
                        <i class="table--search--input--icon fas fa-search "></i>
                        <input class="table--search--input" type="text" placeholder="Search invoice" v-model="search_invoice" @keyup="search()">
                    </div>
                </div>

                <div class="table--heading">
                    <p>ID</p>
                    <p>Date</p>
                    <p>Number</p>
                    <p>Customer</p>
                    <p>Due Date</p>
                    <p>Total</p>
                </div>

                <!-- item 1 -->
                <div class="table--items" v-for="invoice in invoices" :key="invoice.id" v-if="invoices.length > 0">
                    <a href="#" class="table--items--transactionId">#{{invoice.id}}</a>
                    <p>{{invoice.date}}</p>
                    <p>#{{invoice.number}}</p>
                    <p v-if="invoice.customer">{{invoice.customer.firstname}}</p>
                    <p v-else></p>
                    <p>{{invoice.due_date}}</p>
                    <p> $ {{invoice.total}}</p>
                </div>
                <div class="table--items" v-else>
                    <p>Invoice not found</p>
                </div>
            </div>

        </div>
    </div>
</template>
<script setup>
import {onMounted, ref, watch} from "vue";
import axios from "axios";

let invoices = ref([])
let search_invoice = ref([])

const getInvoices = async () => {
    const response = await axios.get("/api/get_all_invoice")
    invoices.value = response.data
}

const search = async () => {
    // Cách 1:
    const response = await axios.get("/api/search_invoice?s="+search_invoice.value)
    // Cách 2:
    // const response = await axios.get("/api/search_invoice", {
    //     params: {
    //         search_invoice: search_invoice.value
    //     }
    // })
    invoices.value = response.data
}

onMounted(async () => {
    getInvoices()
})

</script>
