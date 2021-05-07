/*
 * @Author: Bin
 * @Date: 2021-05-07
 * @FilePath: /PaperAirplane/resources/js/pages/admin/store/UserStore.js
 */

import { makeAutoObservable } from "mobx";

class UserStore {
    me = {};

    constructor() {
        makeAutoObservable(this);
    }

    setMe(me) {
        this.me = me;
    }
}

export default new UserStore();
