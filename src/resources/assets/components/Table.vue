<template>
    <div class="justify-center w-full">
        <table class="text-left w-full data-table">
            <thead class="bg-grey-darkest text-grey-dark text-white w-full">
                <tr class="w-full text-grey-dark">
                    <th v-for="column in columns" :key="column.name" @click="$emit('sort', column.name)"
                        :class="'p-3 w-1/' + columns.length + ' text-' + column.align"
                        class="border">
                        <i class="fa fa-sort text-white" aria-hidden="true"></i>
                        {{column.label}}
                    </th>
                </tr>
            </thead>
            <slot></slot>
        </table>
    </div>
</template>

<script>
export default {
    props: {
        columns: {
            type: Array,
            default: () => ([])
        },
        sortKey: {
            type: String,
            default: ''
        },
        sortOrders :{
            type: Object,
            default: () => ({})
        }
    }
}
</script>

<style scoped>
th {
    cursor: pointer;
}
</style>
