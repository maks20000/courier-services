import Vue from 'Vue'
import Vuex from 'Vuex'

Vue.use(Vuex);

export default new Vuex.Store ({
    state: {
        calculator: [{id:0}],
        regionList: null,
    }
});