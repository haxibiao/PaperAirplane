import React from "react";
import { Dropdown } from "element-react";
import UserStore from "../store/UserStore";
import { observer } from "mobx-react";

const AppHeader = observer((props) => {
    return (
        <header className="app-header">
            <div>
                <a className="logo" style={{ textDecoration: "none" }}>
                    <h3 className="title" style={{}}>
                        飞书纸飞机管理平台
                    </h3>
                </a>
            </div>

            {UserStore.me.id ? (
                <div className="user-info">
                    <Dropdown
                        trigger="click"
                        onCommand={(value) => {
                            console.log("控制", value);

                            switch (value) {
                                case "logout":
                                    window.location.replace("/logout");
                                    UserStore.setMe(null);
                                    break;
                            }
                        }}
                        menu={
                            <Dropdown.Menu>
                                <Dropdown.Item command="edit">
                                    修改资料
                                </Dropdown.Item>
                                <Dropdown.Item command="control">
                                    管理中心
                                </Dropdown.Item>
                                <Dropdown.Item command="logout" divided>
                                    退出登陆
                                </Dropdown.Item>
                            </Dropdown.Menu>
                        }
                    >
                        <div className="avatar-div">
                            <img
                                className="avatar-img"
                                src={UserStore.me?.fs_user_avatar_url || ""}
                            />
                            <i className="el-icon-caret-bottom icon-ic"></i>
                        </div>
                    </Dropdown>
                </div>
            ) : null}
        </header>
    );
});

export default AppHeader;
