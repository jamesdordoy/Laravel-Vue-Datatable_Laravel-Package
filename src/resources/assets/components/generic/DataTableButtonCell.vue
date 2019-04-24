<template>
    <small-button @click="click(data)" title="Update">
        <span><i class="fa fa-edit" aria-hidden="true"></i></span>
    </small-button>
</template>

<script>
export default {
    props: {
        data: {},
        name: {},
        click: {}
    }
}
</script>