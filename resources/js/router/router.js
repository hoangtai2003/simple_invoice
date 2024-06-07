import { createRouter, createWebHistory} from "vue-router";
import invoiceIndex from '../components/invoices/index.vue'
import newInvoice from '../components/invoices/createInvoice.vue'
import showInvoice from '../components/invoices/show.vue'
import invoiceEdit from '../components/invoices/edit.vue'
import notFound from '../components/NotFound.vue';
const routes = [
    {
        path: '/',
        component: invoiceIndex
    },
    {
      path: '/invoice/create',
      component: newInvoice
    },
    {
      path: '/invoice/show/:id',
      component: showInvoice,
      props: true
    },
    {
      path: '/invoice/edit/:id',
      component: invoiceEdit,
      props: true
    },
    {
        path: '/:pathMatch(.*)*',
        component: notFound
    }
]
const router = createRouter({
    history: createWebHistory(),
    routes

})
export default router
