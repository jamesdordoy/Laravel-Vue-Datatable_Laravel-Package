<template>
    <div class="flex w-full">
        <div class="flex mt-4 w-1/2">
            <div class="inline-block relative w-32">
                <select
                    @change="getData()"
                    v-model="tableData.length"
                    class="input-generic focus:outline-none focus:bg-white">
                    <option
                        :key="index"
                        :value="records"
                        v-for="(records, index) in perPage">{{records}}</option>
                </select>
                <div class="pointer-events-none absolute pin-y pin-r flex items-center px-2 text-grey-darker">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="flex mt-4 w-1/2 justify-end">
            <div class="flex w-1/2 justify-end"></div>
            <div class="flex w-1/2 justify-end">
                <text-input
                    name="name"
                    @input="getData()"
                    v-model="tableData.search"
                    placeholder="Search Table">
                </text-input>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        getData() {
            this.$emit("getData");
        }
    },
    props: [
        "tableData", "perPage"
    ]
}
</script>

<style>

</style>
