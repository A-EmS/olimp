<template>
    <div>
        <transition name="fade" mode="out-in" appear>
            <div class="h-100">
                <b-row class="h-100 no-gutters">
                    <b-col lg="12" md="12" class="h-100 d-flex bg-white justify-content-center align-items-center">
                        <b-col lg="9" md="10" sm="12" class="mx-auto app-login-box">
                            <div class="app-logo"/>
                            <h4 class="mb-0">
<!--                                <div>Welcome back,</div>-->
<!--                                <span>Please sign in to your account.</span>-->
                            </h4>
                            <div class="divider"/>
                            <div>
                                <Form>
                                    <b-row form>
                                        <b-col md="6">
                                            <b-form-group>
                                                <Label for="exampleEmail">Login</Label>
                                                <b-form-input v-model="loginvalue" type="text" name="Login" id="exampleLogin"
                                                              placeholder="Login here..."/>
                                            </b-form-group>
                                        </b-col>
                                        <b-col md="6">
                                            <b-form-group>
                                                <Label for="examplePassword">Password</Label>
                                                <b-form-input v-model="passvalue" type="password" name="password" id="examplePassword"
                                                              placeholder="Password here..."/>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
<!--                                    <b-form-checkbox name="check" id="exampleCheck">-->
<!--                                        Keep me logged in-->
<!--                                    </b-form-checkbox>-->
                                    <div class="divider"/>
                                    <div class="d-flex align-items-center">
                                        <div class="ml-auto">
                                            <a href="javascript:void(0);" class="btn-lg btn btn-link">Recover
                                                Password</a>
                                            <b-button v-on:click="login()" variant="primary" size="lg">Login</b-button>
                                        </div>
                                    </div>
                                </Form>
                            </div>
                        </b-col>
                    </b-col>
                </b-row>
            </div>
        </transition>
    </div>
</template>

<script>
    import axios from 'axios';
    import qs from 'qs';

    export default {
        components: {

        },
        created: function () {

        },
        data: () => {
            return {
                loginvalue: '',
                passvalue: '',
            }
        },

        methods: {

            login () {
                var loginData = qs.stringify({
                    username: this.loginvalue,
                    password: this.passvalue
                });
                axios.post(window.apiDomainUrl+'/site/login', loginData)
                    .then( (response) => {
                        if(response.data !== false){
                            this.$store.state.user = response.data;

                            if (typeof this.$store.state.user.settings != 'undefined' && typeof this.$store.state.user.settings.interface_language != 'undefined'){

                            axios.get(window.apiDomainUrl+'/interface-vocabularies/get-interface-vocabulary?language='+this.$store.state.user.settings.interface_language, qs.stringify({}))
                                .then( (response) => {
                                    if(response.data !== false){
                                        this.$store.state.currentInterfaceVocabulary = response.data;
                                        localStorage.setItem('currentInterfaceVocabulary', JSON.stringify(response.data));
                                        this.$router.push({ name: "main" });
                                    }
                                })
                                .catch(function (error) {
                                    console.log(error);
                                });
                            } else {
                                 this.$router.push({ name: "main" });
                            }

                        }
                    })
                    .catch(function (error) {

                    });
            }
        }
    }
</script>
