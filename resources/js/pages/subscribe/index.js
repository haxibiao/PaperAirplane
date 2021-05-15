/*
 * @Author: Bin
 * @Date: 2021-05-15
 * @FilePath: /PaperAirplane/resources/js/pages/subscribe/index.js
 */

import React from "react";
import ReactDOM from "react-dom";
import App from "./App";

// 获取页面 meta 标签传递过来的数据
let user = {}; // 当前登陆的用户
let appID; // 订阅 APP 的 ID
try {
    user = JSON.parse(
        document.getElementsByTagName("meta")["user"]?.content || {}
    );
    appID = document.getElementsByTagName("meta")["app_id"]?.content || {};
} catch (error) {}

//  加载 React APP
ReactDOM.render(
    <App user={user} appID={appID} />,
    document.getElementById("root")
);
