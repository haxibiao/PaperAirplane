import React from "react";

import { Button } from "element-react";
import { useHistory } from "react-router";

export default function index() {
    const history = useHistory();
    return (
        <div className="screen-default">
            <p style={{color: '#0008'}}>ERROR URL: {history?.location?.pathname}</p>
            <h2 style={{ marginBottom: 40 }}>404 这里好像不见啦！</h2>
            <Button
                onClick={() => {
                    history.replace("/");
                }}
            >
                返回主页
            </Button>
        </div>
    );
}
