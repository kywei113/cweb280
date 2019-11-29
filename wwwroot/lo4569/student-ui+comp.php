<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- https://bootswatch.com themes: cerulean cosmo cyborg darkly flatly journal litera lumen lux
	materia	minty pulse sandstone simplex sketchy slate solar spacelab superhero united yeti -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/portal-vue/dist/portal-vue.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/http-vue-loader/src/httpVueLoader.js"></script>


    <title>Student UI + Comp</title>
</head>
<body>
<div class="jumbotron text-center p-5">
    <h1>Student UI + Comp</h1>
</div>

<div class="container" id="managed_by_vue_js">

    <b-modal id="modal-1" title="Record Deletion Confirmation" no-close-on-backdrop no-close-on-esc
             hide-header-close v-model="showModal" @ok="deleteStudent">
        <p class="my-4">Are you sure you want to delete {{this.confirmStudent.givenName}}'s record?</p>
    </b-modal>

    <table class="table table-sm table-hover table-striped">
        <thead>
        <tr class="table-primary">
            <th>StudentID</th>
            <th>Family Name</th>
            <th>Given Name</th>
            <th>Preferred Name</th>
            <th>User Name</th>
        </tr>
        </thead>

        <!-- INPUT NEW STUDENT-->
        <tr is="student-input" class="table-info" @save="sendStudent"></tr>
        <!-- to use properties, convert your property name to kebab case. hideSave -> hide-save -->

        <!-- STUDENT LIST-->
        <tr is="student-input" v-for="stu in students"
            :key="stu.studentID"
            :student="stu" :edit-mode="editID === stu.studentID"
            @save="sendStudent"
            @select="editID != stu.studentID ? editID=stu.studentID : ''"
            @cancel="editID=0" @delete="showErrorModal"
        ></tr>
    </table>

    <footer class="row bg-info">
        <div class="col-sm-7">
            <h3>Vardump Vue Data</h3>
            <pre>{{$data}}</pre>
        </div>
        <div class="col-sm-5">
            <h3>Vardump axios result</h3>
            <pre>{{axiosResult}}</pre>
        </div>
    </footer>
</div>


<script>
    new Vue({
        el: '#managed_by_vue_js',
        data: {
            students: [],
            axiosResult: {},
            editID: 0,
            confirmStudent:{},
            showModal:false
        },
        methods: {
            getData: function () {
                axios.get('students-api.php', {params: {}})
                    .then(response => {
                        this.axiosResult = response;//ONLY FOR DEBUG
                        this.students = response.data;
                    })
                    .catch(errors => {
                        let response = errors.response;
                        this.axiosResult = response;//ONLY FOR DEBUG
                        if (response.status === 404) {//NOT FOUND
                            alert(response.config.url + " was not found");
                        }
                    })
                    .finally();
            },
            sendStudent: function (stu, errorMessages, status) {
                //Important: If you do not use a formData object, then PHP will not like you and not fill the $POST superglobal.
                axios({
                    method: stu.studentID ? 'put' : 'post',
                    url: 'student-api.php',
                    data: stu

                }).then(response => {
                    this.axiosResult = response;
                    status.code = 1; //Tell the component to clear out the text boxes or update student
                    if (response.status === 201) {
                        //Add the new student with the id and username to the students array
                        this.students.unshift(response.data);
                        //Clear out the form inputs

                    } else if (response.status === 200) {

                        //exit edit mode
                        this.editID = 0;

                    }
                }).catch(errors => { //Status code 400 - 600
                    let response = errors.response;
                    this.axiosResult = response;//ONLY FOR DEBUG

                    status.code = 0; // Tell component to not change anything.
                    if (response.status === 422) {
                        Object.assign(errorMessages, response.data); // Save errors messages to a variable to be used later.
                    } else if (response.status === 418) {
                        this.sqlDebug = response.data;
                    }
                })
            },
            showErrorModal: function () {
                this.showModal = true;
                this.confirmStudent = student;
            },
            deleteStudent: function () {
                axios.delete('student-api.php', {params: {editID: this.editID}})
                    .then(response => {
                        this.showModal = false;
                        this.axiosResult = response;//ONLY FOR DEBUG
                        if(response.status === 204)
                        {
                            //Minicise 36 : Do the following
                            //Find student in array
                            //Source : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/findIndex
                            let index = this.students.findIndex(student => student.studentID === response.config.params.editID);

                            //remove student from array
                            //Source : https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/splice
                            this.students.splice(index,1);
                            //Cancel edit.
                            this.editID = 0;
                        }
                    })
                    .catch(errors => { //Status code 400 - 600

                        let response = errors.response;
                        this.axiosResult = response;//ONLY FOR DEBUG

                        if (response.status === 418) {
                            this.sqlDebug = response.data;
                        }
                    })
            }
        },
        components: {
            'student-input': httpVueLoader('./StudentInput.vue')
        },
        mounted() {
            this.getData();
        }
    });
</script>

</body>
</html>