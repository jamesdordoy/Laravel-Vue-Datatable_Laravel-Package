<template>
    <div class="flex flex-col">
        <div class="w-full">
            <data-table-filters
                :tableData="tableData"
                :per-page="perPage"
                @getData="getData">
            </data-table-filters>
            <vue-table
                @sort="sortBy"
                :sortKey="sortKey"
                :columns="columns"
                :sortOrders="sortOrders">
                <tbody
                    class="items-center justify-between w-full text-grey-light border-l border-r border-b border-grey-darkest rounded-b bg-nav">
                    <tr
                        :key="item.id"
                        v-for="item in data">
                        <td 
                            :key="column.name"
                            v-for="column in columns"
                            :class="'p-3 w-1/' + columns.length">
                            <data-table-cell
                                :value="item"
                                :name="column.name"
                                :classes="'text-' + column.align"
                                :click-event="column.click"
                                :comp="column.component">
                            </data-table-cell>
                        </td>
                    </tr>
                </tbody>
            </vue-table>
            <pagination
                :links="pagination.links"
                :pagination="pagination.meta"
                @prev="getData(pagination.links.prev)"
                @next="getData(pagination.links.next)">
            </pagination>
        </div>
    </div>
</template>

<script>
export default {
    created() {
        this.getData();
    },
    mounted() {
        this.columns.forEach((column) => {
           this.sortOrders[column.name] = -1;
        });
    },
    data() {
        return {
            data: [],
            sortKey: 'id',
            sortOrders: {},
            tableData: {
                draw: 0,
                length: this.perPage[0],
                search: '',
                column: 0,
                dir: 'asc',
            },
            pagination: {},
        }
    },
    props: {
        url: {
            type: String,
            default: "/"
        },
        columns: {
            type: Array,
            default: () => ([])
        },
        perPage: {
            type: Array,
            default: () => ([])
        }
    },
    methods: {
        getData(url = this.url) {
            this.tableData.draw++;
            
            axios.get(url, this.getRequestPayload)
            .then(response => {
                let data = response.data;
                if (this.tableData.draw == data.payload.draw) {
                    this.data = data.data;
                    this.pagination = data;
                }
            })
            .catch(errors => {
                console.log(errors);
            });
        },
        sortBy(key) {
            this.sortKey = key;
            this.sortOrders[key] = this.sortOrders[key] * -1;
            this.tableData.column = this.getIndex(this.columns, 'name', key);
            this.tableData.dir = this.sortOrders[key] === 1 ? 'asc' : 'desc';
            this.getData();
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
    },
    computed: {
        getRequestPayload() {
            return {
                params: this.tableData
            };
        }
    }
}
</script>

<style>

tr {
    border-bottom: 1px solid grey;
}

</style>
