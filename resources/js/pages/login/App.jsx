import React, { useState } from "react";

import { Form, Input, Button, Layout } from "element-react";
import { AppHeader } from "../../components";

export default function App() {
    const [account, setaccount] = useState("");
    const [password, setpassword] = useState("");

    return (
        <div>
            <AppHeader />

            <div className="app-content">
                <div className="login-div">
                    <h1 className="title">欢迎回来，请登陆…</h1>
                    <Form model={() => {}} labelWidth="80" onSubmit={() => {

                    }}>
                        <Form.Item label="账号">
                            <Input
                                value={account}
                                onChange={(value) => setaccount(value)}
                            ></Input>
                        </Form.Item>

                        <Form.Item label="密码">
                            <Input
                                value={password}
                                type="password"
                                onChange={(value) => setpassword(value)}
                            ></Input>
                        </Form.Item>

                        <Form.Item>
                            <Button type="primary" nativeType="submit">
                                立即登陆
                            </Button>
                        </Form.Item>
                    </Form>

                    <Layout.Row className="other" gutter="20">
                        <Button className="feishu" type="info" onClick={() => {
                            window.location.replace("/login/tofeishu")
                        }}>
                            飞书登陆
                        </Button>
                        <Button className="feishu" type="info">
                            其他账号
                        </Button>
                    </Layout.Row>
                </div>
            </div>
            <div className="app-footer"></div>
        </div>
    );
}
