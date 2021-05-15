import React from "react";
import { Placeholder, Icon, Divider } from "rsuite";
const { Paragraph } = Placeholder;

export default function Error(props) {
    const { message } = props;
    return (
        <div className="app-content">
            <div className="view-head">
                <Paragraph graph="square" />
                <div className="icon">
                    <Icon
                        style={{ color: "#f2f2f5" }}
                        className="ic-link"
                        icon="link"
                        size="lg"
                    />
                </div>
                <Paragraph graph="circle" />
            </div>
            <div className="view-msg">
                <Paragraph />
            </div>
            <Divider />
            <div className="view-head" style={{ padding: 40 }}>
                <Icon
                    style={{
                        color: "#000A",
                        fontSize: "8rem",
                        marginRight: 20,
                    }}
                    className="ic-link"
                    icon="meh-o"
                    size="5x"
                />
            </div>
            <div
                style={{
                    flex: 1,
                    textAlign: "center",
                    marginTop: 50,
                    color: "#000",
                }}
            >
                <h5>{message || "错误，好像出现了一些问题，请联系开发者。"}</h5>
            </div>
            <div className="view-bar">
                <Placeholder.Graph active style={{ height: 50 }} />
            </div>
        </div>
    );
}
