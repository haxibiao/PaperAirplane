import React, { useState, useEffect } from "react";
import { Table, Button } from "element-react";

import useAxios from "axios-hooks";
import axios from "axios";
import { observer } from "mobx-react";
import UserStore from "../../../../store/UserStore";

function Index() {
    const [{ data, loading, error }, refetch] = useAxios({
        url: "/api/user/list",
        headers: { Authorization: `Bearer ${UserStore.me?.api_token || ""}` },
    });

    const columns = [
        {
            label: "ID",
            prop: "id",
            width: 80,
        },
        {
            label: "飞书 ID",
            prop: "fs_user_id",
            width: 120,
        },
        {
            label: "账号",
            prop: "name",
            width: 180,
        },
        {
            label: "姓名",
            prop: "fs_user_name",
        },
        {
            label: "操作",
            fixed: "right",
            width: 160,
            render: () => {
                return (
                    <span>
                        <Button type="text" size="small">
                            查看
                        </Button>
                        <Button type="text" size="small">
                            编辑
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
