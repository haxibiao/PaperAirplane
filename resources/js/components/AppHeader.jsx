import React from "react";
import { Dropdown } from "element-react";

export default function AppHeader(props) {
    const { user } = props;
    return (
        <header className="app-header">
            <div>
                <a className="logo" style={{ textDecoration: "none" }}>
                    <h3 className="title" style={{}}>
                        飞书纸飞机管理平台
                    </h3>
                </a>
            </div>

            <div className="user-info">
                <Dropdown
                    trigger="click"
                    onCommand={(value) => {
                        console.log("控制", value);
                    }}
                    menu={
                        <Dropdown.Menu>
                            <Dropdown.Item command="edit">修改资料</Dropdown.Item>
                            <Dropdown.Item command="control">管理中心</Dropdown.Item>
                            <Dropdown.Item command="logout" divided>退出登陆</Dropdown.Item>
                        </Dropdown.Menu>
                    }
                >
                    <div className="avatar-div">
                        <img
                            className="avatar-img"
                            src="https://avatars.githubusercontent.com/u/39079814?s=60&v=4"
                        />
                        <i className="el-icon-caret-bottom icon-ic"></i>
                    </div>
                </Dropdown>
            </div>
        </header>
    );
}
