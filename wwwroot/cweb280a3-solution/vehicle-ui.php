<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- https://bootswatch.com themes: cerulean cosmo cyborg darkly flatly journal litera lumen lux
	materia	minty pulse sandstone simplex sketchy slate solar spacelab superhero united yeti -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/portal-vue/dist/portal-vue.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/http-vue-loader/src/httpVueLoader.js"></script>

    <title>Vehicle UI</title>
</head>
<body>
<div id="managed_by_vue_js">
    <div class="jumbotron text-center p-4">
        <h1>Vehicle UI</h1>
    </div>
    <div class="container mb-5">
        <vehicle-input :modal-id="modalID" :vehicle="currentVehicle" @save="saveVehicle" show-debug></vehicle-input>
        <vehicle-table :table-id="tableID" provider-url="vehicle-api.php" @edit="editStart" @changed="debugTable" :per-page="25"></vehicle-table>
    </div>

    <button @click="$root.$emit('bv::refresh::table',tableID)">Refresh</button>
    <button @click="editStart({vehicleID:null,make:'fake make',model:'not real',type:'Geometric',year:'made this up'},-1)">Open</button>

    <footer class="bg-info row">
        <div class="col-sm-6">
            <h3>Vardump Vue Data</h3>
            <pre>{{$data}}</pre>
        </div>
        <div class="col-sm-6">
            <h3>Vardump axios result</h3>
            <pre>{{axiosResult}}</pre>
        </div>
    </footer>
</div>
<script>
    new Vue({
        el: '#managed_by_vue_js',
        data: {
            tableItems:[],
            modalID:'modal-vehicle-input',
            tableID:'table-vehicle-list',
            currentVehicle: {vehicleID:null,make:null,model:null,type:null,year:null},
            axiosResult: {},
            debugSQL:''
        },
        methods:{
            editStart:function(vehicle,index){
                this.currentVehicle = vehicle;
                this.$bvModal.show(this.modalID);
            },
            saveVehicle: function (vehicle, errors, status) {
                axios.request({method: vehicle.vehicleID ? 'put' : 'post', url: 'vehicle-api.php', data: vehicle})
                    .then(resp => {
                        this.axiosResult = resp;//ONLY FOR DEBUG
                        status.code = 1;
                        this.$bvModal.hide(this.modalID);
                        if (resp.status == 201) {//Vehicle created in db
                            this.$root.$emit('bv::refresh::table', this.tableID)
                        } else if (resp.status == 200) {//Vehicle updated in db

                        }
                    })
                    .catch(err => {
                        let resp = err.response;
                        this.axiosResult = resp;//ONLY FOR DEBUG

                        status.code = 0;
                        if (resp.status == 422) {
                            Object.assign(errors, resp.data);
                        } else if (resp.status == 409) {
                            this.debugSQL = resp.data;
                            this.$bvModal.hide(this.modalID);
                        }
                    })
            },
            debugTable:function(allTableItems,visibleTableItems){
                this.tableItems = visibleTableItems;
                this.axiosResult= allTableItems;
            }
        },
        components:{
            'vehicle-table': httpVueLoader('./VehicleTable.vue'),
            'vehicle-input': httpVueLoader('./VehicleInput.vue')
        }
    });
</script>

</body>
</html>