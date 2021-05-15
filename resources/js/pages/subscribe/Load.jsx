import React from "react";
import { Placeholder, Icon, Divider } from "rsuite";
const { Paragraph } = Placeholder;

export default function Load() {
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
            <div className="view-info">
                <Placeholder.Grid rows={5} columns={1} active />
                <Paragraph style={{ margin: "30px 0" }} />
            </div>
            <div className="view-bar">
                <Placeholder.Graph active style={{ height: 50 }} />
            </div>
        </div>
    );
}
