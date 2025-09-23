<template>
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="float-end">
                            <a href="/customer_page" class="btn btn-success mx-3 btn-sm">
                                Back
                            </a>
                        </div>
                        <form @submit.prevent="submit">
                            <div class="card-body">
                                <h4>Save Customer</h4>
                                <input id="id" hidden name="id" v-model="form.id" placeholder="Customer ID"
                                    class="form-control" type="text" />
                                <br />
                                <input id="name" name="name" v-model="form.name" placeholder="Customer Name"
                                    class="form-control" type="text" />
                                <br />
                                <input id="email" name="email" v-model="form.email" placeholder="Customer Email"
                                    class="form-control" type="email" />
                                <br />
                                <input id="mobile" name="phone" v-model="form.mobile" placeholder="Customer Phone"
                                    class="form-control" type="text" />
                                <br />
                                <button type="submit" class="btn w-100 btn-success">Save Change</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
 import { useForm, usePage, router, Link } from '@inertiajs/vue3';
 import { createToaster } from '@meforma/vue-toaster';

const toaster = createToaster({
    position: 'top-right'
});

const urlParams = new URLSearchParams(window.location.search);
let id = ref(parseInt(urlParams.get('id')));

const form = useForm({
    id: id.value,
    name: '',
    email: '',
    mobile: ''
});

const page = usePage();

let URL = '/customer_create';
let list = page.props.customer;

if(id.value !== 0 && list !== null){
    URL = '/customer_update';
    form.name = list.name;
    form.email = list.email;
    form.mobile = list.mobile;
}

function submit(){
    if(form.name.length ==0 ){
        toaster.warning('Customer name is required');
    }else if(form.email.length ==0 ){
        toaster.warning('Customer email is required');
    }else if(form.mobile.length ==0 ){
        toaster.warning('Customer phone is required');
    }else{
        form.post(URL, {
            onSuccess: () => {
                if(page.props.flash.status === true){
                    toaster.success(page.props.flash.message);
                    router.get('/customer_page');
                }else{
                    toaster.error(page.props.flash.message);
                }
            }
        });
    }
}
</script>
