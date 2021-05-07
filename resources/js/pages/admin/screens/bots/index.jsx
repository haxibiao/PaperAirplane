import React, { useState, useEffect } from "react";
import { Table, Button } from "element-react";

import useAxios from "axios-hooks";
import axios from "axios";
import { observer } from "mobx-react";
import UserStore from "../../../../store/UserStore";

function Index() {
    const [{ data, loading, error }, refetch] = useAxios({
        url: "/api/bot/list",
        headers: { Authorization: `Bearer ${UserStore.me?.api_token || ""}` },
    });

    const columns = [
        {
            label: "ID",
            prop: "id",
            width: 80,
        },
        {
            label: "飞书 APP ID",
            prop: "fs_app_id",
            width: 220,
        },
        {
            label: "管理员",
            prop: "user.name",
            width: 180,
        },
        {
            label: "备注",
            prop: "remarks",
        },
        {
            label: "操作",
            fixed: "right",
            width: 180,
            render: () => {
                return (
                    <span>
                        <Button type="text" size="small">
                            创建应用
                        </Button>
                        <Button type="text" size="small">
                            修改配置
                        </Button>
                    </span>
                );
            },
        },
    ];

    const [list, setlist] = useState([]);
    useEffect(() => {
        if (data) {
            const new_data = data?.data?.data;
            setlist(new_data);
        }
    }, [data]);

    return (
        <div className="screen-users">
            <Table
                style={{ width: "100%" }}
                columns={columns}
                maxHeight={200}
                data={list}
            />
        </div>
    );
}

export default observer(Index);
