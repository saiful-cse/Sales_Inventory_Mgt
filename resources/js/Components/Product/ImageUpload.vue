<template>
    <div>
        <label for="image" class="me-2">
            <img :src="(preview || currentImage ) ?? 'placeholderimage.png'" class="img-thumbnail" height="50px" width="50px" />
        </label>
        <input @input="imageSelected($event)" type="file" name="image" id="image" />
    </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    productImage: String
});

const currentImage = props.productImage? props.productImage : null;
const preview = ref(currentImage);

const emit = defineEmits('image');

const imageSelected = (event) => {
    const file = event.target.files[0];
    if (file) {
        preview.value = URL.createObjectURL(file);
        emit('image', file);
    }
};

</script>
