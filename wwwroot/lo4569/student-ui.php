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

    <style>
        @keyframes bop
        {
            0% {background-color: deepskyblue;}
            33% {background-color: orange;}
            66% {background-color: hotpink;}
            100% {background-color: deepskyblue;}
        }
        @keyframes rgb
        {
            0% {background-color: red;}
            33% {background-color: greenyellow;}
            66% {background-color: deepskyblue;}
            100% {background-color: red;}
        }

        @keyframes rgbText
        {
            0% {color: red; background-color: greenyellow;}
            33% {color: greenyellow; background-color: deepskyblue;}
            66% {color: deepskyblue; background-color: red;}
            100% {color: red; background-color: greenyellow;}
        }


        .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
            animation-name: bop;
            animation-duration: 1s;
            animation-iteration-count: infinite;
            color: black;
        }

        .tdEdit
        {
            animation-name: rgb;
            animation-duration: 0.5s;
            animation-iteration-count: infinite;
            color: deepskyblue;
        }
        .tdEdit input
        {
            background-color: lawngreen;
            animation-name:rgbText;
            animation-duration: 0.5s;
            animation-iteration-count: infinite;
        }

        .tdEdit button
        {
            animation-name:rgbText;
            animation-duration: 0.5s;
            animation-iteration-count: infinite;
        }


    </style>
    <title>Title</title>
</head>
<body>
<div class="jumbotron text-center">
    <h1>Student - UI</h1>
</div>
<div class="container" id="managed_by_vue_js">
    <b-modal title="Confirm Delete" v-model="showDelPrompt" hide-header-close @ok="deleteStudent" @shown="fixBackdrop">
            Are you sure you want to delete {{confirmStudent.givenName}} {{confirmStudent.familyName}}?
    </b-modal>

    <!--https://bootstrap-vue.js.org/docs/components/form-group/-->
<!--    <b-form-group :invalid-feedback="newErrors.familyName" :state="newStates.familyName">-->
<!--        <b-form-input v-model="newStudent.familyName" :state="newStates.familyName" trim></b-form-input>-->
<!--    </b-form-group>-->

    <div class="row">
        <b-input-group class="offset-lg-8 col-lg-4 col-12">
            <b-form-input type="search" placeholder="SEARCH" debounce="500" @update="getData" v-model="searchString"></b-form-input>

            <b-input-group-append>
                <b-button class="fas fa-search fa-xl" @click="getData"></b-button>
            </b-input-group-append>
        </b-input-group>
    </div>

    <div v-if="loading" class="text-center">
        <b-spinner variant="primary" label="Spinning" size="lg"></b-spinner>
    </div>

    <div v-if="sqlDebug.length" class="alert-warning">
        {{sqlDebug}}
    </div>

    <!--Student Table-->
    <table v-if="!loading" class="table table-dark table-striped table-bordered table-hover">
        <tr>
            <th scope="col">Student ID</th>
            <th scope="col">Family Name</th>
            <th scope="col">Given Name</th>
            <th scope="col">Preferred Name</th>
            <th scope="col">Username</th>
        </tr>

        <!--MINICISE 32 - Move the -form-group into the following th tags for the appropriate fields-->
        <tr @click="editCancel()">
            <th>{{newStudent.id}}</th>

            <th><b-form-group :invalid-feedback="newErrors.familyName" :state="newStates.familyName">
                    <b-form-input v-model="newStudent.familyName" :state="newStates.familyName" @update="newErrors.familyName = null"trim></b-form-input>
                </b-form-group></th>

            <th><b-form-group :invalid-feedback="newErrors.givenName" :state="newStates.givenName">
                    <b-form-input v-model="newStudent.givenName" :state="newStates.givenName" @update="newErrors.givenName = null"trim></b-form-input>
                </b-form-group></th>

            <th><b-form-group :invalid-feedback="newErrors.preferredName" :state="newStates.preferredName">
                    <b-form-input v-model="newStudent.preferredName" :state="newStates.preferredName" @update="newErrors.preferredName = null"trim></b-form-input>
                </b-form-group></th>

            <th><button class="btn btn-primary far fa-save" title="Save" @click="postStudent"></button></th>
        </tr>

        <tr class="table-warning" v-if="!students.length"><td colspan="5" class="text-center">No Students Found</td></tr>

        <!--Can do "@click="editID == stu.id ? '' : editStart(stu)"" as alternative to the If within editStart-->
        <tr v-for="stu in students" :key="stu.studentID" scope="row" @click="editStart(stu)">    <!--BEST PRACTICE - Vue asks that you specify the unique identifier (key) in the object-->
            <!-- MINICISE 23: Output all the fields in the stu object-->

            <!--MINICISE 24: Come up with a conditional statement that will show the appropriate template
                Test by adding @click to tr tag -->
            <template v-if="editID == stu.studentID"> <!-- EDIT MODE -->
                <td class="tdEdit">{{editStudent.studentID}}</td>
                <td class="tdEdit">
                    <b-form-group :invalid-feedback="editErrors.familyName" :state="editStates.familyName">
                        <b-form-input v-model="editStudent.familyName" :state="editStates.familyName" @update="editErrors.editName = null" trim></b-form-input>
                    </b-form-group>
                </td>
                <td class="tdEdit">
                    <b-form-group :invalid-feedback="editErrors.givenName" :state="editStates.givenName">
                        <b-form-input v-model="editStudent.givenName" :state="editStates.givenName" @update="editErrors.givenName = null" trim></b-form-input>
                    </b-form-group>
                </td>
                <td class="tdEdit">
                    <b-form-group :invalid-feedback="editErrors.preferredName" :state="editStates.preferredName">
                        <b-form-input v-model="editStudent.preferredName" :state="editStates.preferredName" @update="editErrors.preferredName = null"trim></b-form-input>
                    </b-form-group>
                </td>
                <td class="tdEdit">
                    <button class="btn btn-primary far fa-save" title="Save" @click.stop="putStudent"></button>
                    <button class="btn btn-primary far fa-window-close" title="Cancel" @click.stop="editCancel"></button>
                    <button class="btn btn-danger far fa-trash-alt" title="Delete" @click.stop="deleteConfirm(editStudent)"></button>
                </td>
            </template>
            <template v-else> <!-- VIEW MODE -->
                <td>{{stu.studentID}}</td>
                <td>{{stu.familyName}}</td>
                <td>{{stu.givenName}}</td>
                <td>{{stu.preferredName}}</td>
                <td>{{stu.userName}}</td>
            </template>

        </tr>
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
            loading: false,
            searchString: '',
            editErrors: {},
            sqlDebug: '',
            newErrors: {},
            invalidFeedback:  "Required Field",
            confirmStudent: {},
            showDelPrompt: false,
            editID: 0,
            editStudent:{},
            newStudent:
                {
                    studentID: null,
                    familyName:'',
                    givenName:'',
                    preferredName:'',
                    userName:''
                },
            students: [],
            axiosResult: {}
        },
        methods: {
            getData: function () {
                this.loading = true;

                axios.get('students-api.php', {params: {searchfor:this.searchString}})
                    .then(response => {
                        this.students = response.data;
                        this.axiosResult = response;//ONLY FOR DEBUG
                    })
                    .catch(errors => {
                        let response = errors.response;
                        this.axiosResult = errors;//ONLY FOR DEBUG
                        if(response.status === 404)  //No students found
                        {
                            this.students = []; //Sets students to an empty array
                        }
                    })
                    .finally(b => {
                        this.loading = false;
                    })
            },
            deleteConfirm: function(student)
            {
                this.showDelPrompt = true;
                this.confirmStudent = student;      //Can't edit, so assign is not needed?
            },
            deleteStudent: function()
            {
              axios.delete('students-api.php', { params: {id:this.confirmStudent.studentID }})
                  .then(response => {       //Status code 200 - 299
                      this.axiosResult = response;

                      if(response.status === 204)   //Successful delete will return a 204 status code
                      {
                          //MINICISE 36: Do the following
                          //Find student in students array, remove it, //Cancel the edit
                          let id = this.students.findIndex(s => s.studentID == response.config.params.id);
                          this.students.splice(id, 1);
                          this.editCancel();
                      }
                  })
                  .catch(errors => {        //Status code 400 - 600
                      let response = errors.response;
                      this.axiosResult = errors;//ONLY FOR DEBUG
                      if(response.status === 418)
                      {
                          this.students = []; //Sets students to an empty array
                      }
                  })
                // this.getData();
            },

            postStudent: function ()
            {
                //IMPORTANT: If you do not use a FormData js object then PHP will not fill the $_POST superglobal
                //BUT there are other ways to access the posted data (JSON)
                this.newErrors = {};
                axios.post('students-api.php', this.newStudent)
                    .then(response => {
                        this.axiosResult = response;//ONLY FOR DEBUG
                        if(response.status == 201)  //Student created and added to DB
                        {
                            //Add new student with the ID and username to Student Array
                            this.students.push(response.data);

                            this.newStudent = {};
                        }

                    })
                    .catch(errors => {
                        let response = errors.response;
                        this.axiosResult = response;//ONLY FOR DEBUG
                        if(response.status == 422)
                        {
                            this.newErrors = response.data;    //Save error messages
                        }
                        else
                        {
                            if(response.status == 418)
                            {
                                // this.newErrors = response.data //Store last SQL statement
                                this.sqlDebug = response.data
                            }
                        }

                    })
                // this.getData();
            },

            putStudent: function()
            {
                this.editErrors = {}; //reset errors in case we get different errors, or no errors from the API

                axios.put('students-api.php', this.editStudent)
                    .then(response => {
                        this.axiosResult = response;
                        if(response.status == 200)
                        {
                            let index = this.students.findIndex((stu => stu.studentID === this.editID));
                            this.students[index] = response.data;
                        }
                        this.editCancel();
                    })
                    .catch(errors => {
                        let response = errors.response;
                        this.axiosResult = response;//ONLY FOR DEBUG
                        if(response.status == 422)
                        {
                            this.editErrors = response.data;    //Save error messages
                        }
                        else
                        {
                            if(response.status == 418)
                            {
                                // this.newErrors = response.data //Store last SQL statement
                                this.sqlDebug = response.data
                            }
                        }
                    })

            },

            editStart: function (student)
            {
                if(this.editID !== student.studentID)
                {
                    this.editErrors = {};
                    this.editID = student.studentID;
                    this.editStudent = Object.assign({}, student);
                }
            },

            editCancel: function ()
            {
                this.editID = 0;
                this.editStudent = {};  //Resets editStudent with a new empty object - {} is shorthand for new empty object
            },

            fixBackdrop: function ()
            {
                document.querySelector('.modal-backdrop').classList.add('show');
            }
        },
        computed: {
                newStates: function()
                {
                    return {
                        // familyName: this.newErrors.familyName ? false : this.newStudent.familyName.length > 0 ? true : null,
                        // givenName: this.newErrors.givenName ? false : this.newStudent.givenName.length > 0 ? true : null,
                        // preferredName: this.newStudent.preferredName.length > 0 ? true : null
                        familyName: this.newErrors.familyName ? false :  null,
                        givenName: this.newErrors.givenName ? false : null,
                        preferredName: this.newErrors.preferredName ? null : null
                    }
                },

                editStates: function()
                {
                    return {
                        familyName: this.editErrors.familyName ? false :  null,
                        givenName: this.editErrors.givenName ? false : null,
                        preferredName: this.editErrors.preferredName ? null : null
                    }
                },

            },
        mounted() {
            this.getData();
        }
    });
</script>

</body>
</html>