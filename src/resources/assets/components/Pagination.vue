<template>
    <nav class="flex mt-4" v-if="!client">
        <div class="w-1/2">
            <span class="text-white">Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} Entries</span>
        </div>
        <div class="flex w-1/2 justify-end">
            <small-button
                classes="mr-4"
                v-if="links.prev"
                @click="$emit('prev');">
                <i class="fa fa-chevron-left" aria-hidden="true"></i>
                &nbsp;Prev
            </small-button>
            <small-button
                v-else
                classes="mr-4"
                :disabled="true" @click="$emit('prev');">
                <i class="fa fa-chevron-left" aria-hidden="true"></i>
                &nbsp;Prev
            </small-button>
            <small-button
                v-if="links.next"
                @click="$emit('next');">
                Next&nbsp;
                <i class="fa fa-chevron-right" aria-hidden="true"></i>
            </small-button>
            <small-button
                v-else
                :disabled="true">
                Next&nbsp;
                <i class="fa fa-chevron-right" aria-hidden="true"></i>
            </small-button>
        </div>        
    </nav>
</template>

<script>
    export default {
        props: {
            pagination: {
                default: () => ({

                })
            },
            links: {
                default: () => ({

                })
            },
            client: {

            }, 
            filtered: {

            }
        }
    }
</script>