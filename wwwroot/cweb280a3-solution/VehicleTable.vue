<template>
    <div>
        <b-table ref="vehicleTable"
        :id="tableId" :items="getItems" :api-url="providerUrl" :fields="fields" :tbody-transition-props="{name:'flip-list'}"
        :sort-compare-options="{ numeric: true, sensitivity: 'base' }" :current-page="currentPage"
        primary-key="vehicleID" head-variant="dark" :per-page="perPage" @context-changed="handleChange" @refreshed="handleChange"
        no-provider-sorting no-provider-paging striped hover sort-icon-left borderless foot-clone >

            <template v-slot:head(actions)="row">
                <button class="btn btn-outline-dark fas fa-plus fa-xl" title="Add New" @click="$emit('edit',undefined,-1)"></button>
            </template>

            <template v-slot:cell(actions)="row">
                <button class="btn btn-outline-secondary fas fa-edit fa-xl"  title="Edit" @click="$emit('edit',row.item,row.index)"></button>
            </template>

            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner style="width: 5rem; height: 5rem;" class="align-middle"></b-spinner>
                    <strong class="d-block" >Loading...</strong>
                </div>
            </template>
            <template v-slot:empty>
                <div class="text-center alert-warning my-2">
                    No Vehicles to Display
                </div>
            </template>
        </b-table>
        <b-pagination v-if="rowCount>perPage" v-model="currentPage" :total-rows="rowCount" :per-page="perPage" align="fill" size="sm" class="my-0"></b-pagination>
    </div>
</template>

<script type="module">
    module.exports = {
        props:{
            providerUrl: {
                type: String,
                required: true
            },
            tableId: {
                type: String,
                default: 'table-vehicle'
            },
            perPage:{// added paging functionality for fun - NOT in assignment
                type:Number,
                default:9999
            }
        },
        data:function(){
            return {
                currentPage:1,
                rowCount:1,
                fields: [{key: 'vehicleID', variant:'secondary', label:'ID', sortable: true, thStyle:{width:'1rem'}},
                    {key: 'make',sortable: true},
                    {key: 'model',sortable: true},
                    {key: 'year',sortable: true},
                    {key: 'type',sortable: true},
                    {key: 'actions', variant:'info', thStyle:{width:'1rem'}, thClass:['bg-info']}
                ]
            }
        },
        methods:{
            getItems: function (ctx) {
                //this.isBusy = true;
                let promise = axios.get(ctx.apiUrl)
                return promise.then(resp => {
                    const items = resp.data
                    //this.isBusy = false;
                    return items
                }).catch(error => {
                    //this.isBusy = false;
                    return []
                })
            },
            handleChange:function() {
                //used refs to get data out of the b-table - NOT in assignment
                this.rowCount = this.$refs.vehicleTable.localItems.length;
                //send the b-table data through the emit so we can debug in the UI
                this.$emit('changed', this.$refs.vehicleTable.localItems,this.$refs.vehicleTable.computedItems)
            }
        }
    }
</script>

<style scoped>
    .flip-list-move {
        transition: transform .3s;
    }
</style>