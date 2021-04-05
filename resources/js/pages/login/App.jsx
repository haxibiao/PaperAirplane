import React from "react";

import { Form, Input, Button, Layout } from "element-react";

export default function App() {
    return (
        <div>
            <header className="app-header">
                <div>
                    <a className="logo" style={{ textDecoration: "none" }}>
                        <h3 className="title" style={{}}>
                            飞书纸飞机管理平台
                        </h3>
                    </a>
                </div>
            </header>
            <div className="app-content">
                <div className="login-div">
                    <h1 className="title">欢迎回来，请登陆…</h1>
                    <Form model={() => {}} labelWidth="80" onSubmit={() => {}}>
                        <Form.Item label="账号">
                            <Input value={""} onChange={() => {}}></Input>
                        </Form.Item>

                        <Form.Item label="密码">
                            <Input value={""} onChange={() => {}}></Input>
                        </Form.Item>

                        <Form.Item>
                            <Button type="primary" nativeType="submit">
                                立即登陆
                            </Button>
                            <Button>取消</Button>
                        </Form.Item>
                    </Form>

                    <Layout.Row className="other" gutter="20">
                        <Button className="feishu" type="info">
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
