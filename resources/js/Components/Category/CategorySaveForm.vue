<template>
    <div class="container-fluid">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="float-end">
                            <Link href="/category_page" class="btn btn-success mx-3 btn-sm">
                                Back
                            </Link>
                        </div>
                        <form @submit.prevent="submit">
                            <div class="card-body">
                                <h4>Save Category</h4>
                                <input id="id" hidden name="id" v-model="form.id" placeholder="Category ID"
                                    class="form-control" type="text" />
                                <br />
                                <input id="name" name="name" v-model="form.name" placeholder="Category Name"
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
import { useForm, usePage, router, Link } from '@inertiajs/vue3';
import {createToaster } from '@meforma/vue-toaster';
import { ref } from 'vue';

const toaster = createToaster({
    position: 'top-right'
});

const ulrParams = new URLSearchParams(window.location.search);
let id = ref(parseInt(ulrParams.get('id')));

const form = useForm({
    id: id.value,
    name: ''
});
const page = usePage();

let URL = '/category_create';
let category = page.props.category;

if(id.value !== 0 && category !== null){
    let URL = '/category_update';
    form.name = category.name;
}

function submit(){
    if(form.name.length ==0 ){
        toaster.warning('Category name is required');
    }else{
        form.post(URL, {
            onSuccess: () => {
                if(page.props.flash.status === true){
                    toaster.success(page.props.flash.message);
                    router.get('/category_page');
                }else{
                    toaster.error(page.props.flash.message);
                }
            }
        });
    }
}

</script>
