import { useState, useEffect } from "react";
import { Head, useForm, router, Link, usePage } from "@inertiajs/react";
import { UserAdd02Icon } from "hugeicons-react";

import Layout from "@/Layouts/Layout";
import { ROLES } from "@/enums/roles";

function Users({ users }) {
    const form = useForm();
    const [selectedUser, setSelectedUser] = useState({});
    const [isUpdate, setIsUpdate] = useState(false);
    const [showFlash, setShowFlash] = useState(false);
    const [isLeaving, setIsLeaving] = useState(false);
    const { flash } = usePage().props;

    useEffect(() => {
        if (flash?.success) {
            setIsLeaving(false);
            setShowFlash(true);
            const timer = setTimeout(() => {
                setIsLeaving(true);
                setTimeout(() => {
                    setShowFlash(false);
                }, 300); // Match animation duration
            }, 3000);
            return () => clearTimeout(timer);
        }
    }, [flash?.success]);

    const {
        data,
        setData,
        post,
        patch,
        processing,
        errors,
        reset,
        clearErrors,
    } = useForm({
        id: "",
        staff_id: "",
        designation: "",
        name: "",
        role: "user",
        email: "",
        password: "",
        password_confirmation: "",
    });

    const submit = (e) => {
        e.preventDefault();

        if (!isUpdate)
            return post(route("user"), {
                onSuccess: () => {
                    router.reload();
                    document.getElementById("modalform").submit();
                    reset();
                },
            });

        patch(route("user"), {
            onSuccess: () => {
                router.reload();
                document.getElementById("modalform").submit();
                setIsUpdate(false);
                reset();
            },
        });
    };

    const handleEditModal = (user) => {
        setData(user);
        setIsUpdate(true);
        document.getElementById("modal_add_staff").showModal();
    };

    const handleDeleteModal = (user) => {
        setSelectedUser(user);
        document.getElementById("modal_delete").showModal();
    };

    const handleDelete = () => {
        form.delete(
            route("user", {
                staff_id: selectedUser.id,
            }),
            {
                onSuccess: () => {
                    router.reload();
                    document
                        .getElementById("delete-modal-form")
                        .requestSubmit();
                },
            }
        );
    };

    return (
        <>
            <Head title="User List" />
            <div className="min-h-screen bg-[#1E1E1E] p-6">
                {showFlash && flash?.success && (
                    <div className={`fixed top-4 right-4 z-50 ${isLeaving ? 'animate-fade-out-up' : 'animate-fade-in-down'}`}>
                        <div className="bg-[#1e3a8a] text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{flash.success}</span>
                        </div>
                    </div>
                )}
                <div className="flex justify-between items-center mb-6">
                    <h1 className="text-2xl font-bold text-white">LIST OF STAFF MEMBERS</h1>
                    <div
                        className="bg-[#1e3a8a] text-white px-4 py-2 rounded-lg flex items-center gap-2"
                        onClick={() => {
                            reset();
                            setIsUpdate(false);
                            document
                                .getElementById("modal_add_staff")
                                .showModal();
                        }}
                    >
                        <UserAdd02Icon className="size-4" />
                        ADD NEW STAFF
                    </div>
                </div>
                {/* //Add and Update user */}
                <dialog id="modal_add_staff" className="modal">
                    <form
                        method="dialog"
                        className="modal-backdrop bg-opacity-50"
                        id="modalform"
                        onSubmit={() => {
                            clearErrors();
                        }}
                    >
                        <button>Close</button>
                    </form>
                    <div className="modal-box bg-[#1A1A2E] text-white">
                        <p className="text-center text-2xl font-bold mb-6 text-white">
                            {isUpdate ? "EDIT STAFF" : "ADD NEW STAFF"}
                        </p>
                        <form onSubmit={submit}>
                            <label className="form-control w-full">
                                <div className="label">
                                    <span className="label-text text-lg text-white">
                                        STAFF ID
                                    </span>
                                </div>
                                <input
                                    id="staff_id"
                                    type="text"
                                    name="staff_id"
                                    value={data.staff_id}
                                    className="input bg-[#0A0A29] text-white border-none w-full"
                                    onChange={(e) =>
                                        setData("staff_id", e.target.value)
                                    }
                                />
                                <div className="label">
                                    <span className="label-text text-error">
                                        {errors.staff_id}
                                    </span>
                                </div>
                            </label>
                            <label className="form-control w-full">
                                <div className="label">
                                    <span className="label-text text-lg text-white">
                                        ROLE
                                    </span>
                                </div>

                                <select
                                    className="select bg-[#0A0A29] text-white border-none w-full"
                                    id="role"
                                    type="text"
                                    name="role"
                                    value={data.role}
                                    onChange={(e) =>
                                        setData("role", e.target.value)
                                    }
                                >
                                    <option value={ROLES.user}>User</option>
                                    <option value={ROLES.admin}>
                                        Admin
                                    </option>
                                </select>

                                <div className="label">
                                    <span className="label-text text-error">
                                        {errors.role}
                                    </span>
                                </div>
                            </label>

                            <label className="form-control w-full">
                                <div className="label">
                                    <span className="label-text text-lg text-white">
                                        NAME
                                    </span>
                                </div>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value={data.name}
                                    className="input bg-[#0A0A29] text-white border-none w-full"
                                    onChange={(e) =>
                                        setData("name", e.target.value)
                                    }
                                />
                                <div className="label">
                                    <span className="label-text text-error">
                                        {errors.name}
                                    </span>
                                </div>
                            </label>
                            <label className="form-control w-full">
                                <div className="label">
                                    <span className="label-text text-lg text-white">
                                        {"DESIGNATION - "}
                                        <span className="italic">
                                            optional
                                        </span>
                                    </span>
                                </div>
                                <input
                                    id="designation"
                                    type="text"
                                    name="designation"
                                    value={data.designation}
                                    className="input bg-[#0A0A29] text-white border-none w-full"
                                    autoComplete="designation"
                                    onChange={(e) =>
                                        setData(
                                            "designation",
                                            e.target.value
                                        )
                                    }
                                />
                                <div className="label">
                                    <span className="label-text text-error">
                                        {errors.designation}
                                    </span>
                                </div>
                            </label>
                            <label className="form-control w-full">
                                <div className="label">
                                    <span className="label-text text-lg text-white">
                                        EMAIL
                                    </span>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    className="input bg-[#0A0A29] text-white border-none w-full"
                                    autoComplete="email"
                                    onChange={(e) =>
                                        setData("email", e.target.value)
                                    }
                                />
                                <div className="label">
                                    <span className="label-text text-error">
                                        {errors.email}
                                    </span>
                                </div>
                            </label>

                            {isUpdate && (
                                <>
                                    <div className="divider text-white">Password Change (Optional)</div>
                                    <label className="form-control w-full">
                                        <div className="label">
                                            <span className="label-text text-lg text-white">
                                                NEW PASSWORD
                                            </span>
                                        </div>
                                        <input
                                            id="password"
                                            type="password"
                                            name="password"
                                            value={data.password}
                                            className="input bg-[#0A0A29] text-white border-none w-full"
                                            onChange={(e) =>
                                                setData("password", e.target.value)
                                            }
                                        />
                                        <div className="label">
                                            <span className="label-text text-error">
                                                {errors.password}
                                            </span>
                                        </div>
                                    </label>

                                    <label className="form-control w-full">
                                        <div className="label">
                                            <span className="label-text text-lg text-white">
                                                CONFIRM NEW PASSWORD
                                            </span>
                                        </div>
                                        <input
                                            id="password_confirmation"
                                            type="password"
                                            name="password_confirmation"
                                            value={data.password_confirmation}
                                            className="input bg-[#0A0A29] text-white border-none w-full"
                                            onChange={(e) =>
                                                setData("password_confirmation", e.target.value)
                                            }
                                        />
                                        <div className="label">
                                            <span className="label-text text-error">
                                                {errors.password_confirmation}
                                            </span>
                                        </div>
                                    </label>
                                </>
                            )}
                            <div className="mt-6 flex justify-end space-x-2">
                                <button
                                    className="bg-[#1e3a8a] text-white px-6 py-2 rounded hover:bg-[#2e4a9a]"
                                    disabled={processing}
                                >
                                    {isUpdate ? "SAVE" : "ADD"}
                                </button>
                                <button
                                    type="button"
                                    className="bg-[#0A0A29] text-white px-6 py-2 rounded hover:bg-[#1A1A2E]"
                                    onClick={() => {
                                        document
                                            .getElementById("modalform")
                                            .requestSubmit();
                                    }}
                                >
                                    Close
                                </button>
                            </div>
                        </form>
                    </div>
                </dialog>
                {/* Delete modal */}
                <dialog id="modal_delete" className="modal">
                    <div className="modal-box bg-[#1A1A2E] text-white">
                        <p className="text-center text-2xl font-bold mb-6">
                            DELETE STAFF
                        </p>
                        <p className="text-center pb-4 text-gray-300">
                            Are you sure to delete this user?
                        </p>
                        <div className="grid grid-cols-2 bg-[#0A0A29] rounded-md p-4">
                            <div>
                                <p className="text-gray-400">Staff ID</p>
                                <p className="text-white">{selectedUser.staff_id}</p>
                            </div>
                            <div>
                                <p className="text-gray-400">Name</p>
                                <p className="text-white">{selectedUser.name}</p>
                            </div>
                        </div>
                        <div className="mt-6 flex justify-end space-x-2">
                            <button
                                className="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700"
                                disabled={processing}
                                onClick={handleDelete}
                            >
                                DELETE
                            </button>
                            <button
                                className="bg-[#0A0A29] text-white px-6 py-2 rounded hover:bg-[#1A1A2E]"
                                onClick={() =>
                                    document
                                        .getElementById("delete-modal-form")
                                        .requestSubmit()
                                }
                            >
                                Close
                            </button>
                        </div>
                    </div>

                    <form
                        id="delete-modal-form"
                        method="dialog"
                        className="modal-backdrop bg-opacity-50"
                    >
                        <button>Close</button>
                    </form>
                </dialog>
                <div className="mx-auto">
                    <div className="overflow-hidden bg-[#0A0A29] shadow-sm rounded-lg">
                        <div className="bg-[#1e3a8a] text-white p-3">
                            STAFF LIST
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse">
                                {/* head */}
                                <thead>
                                    <tr>
                                        <th className="bg-[#1e3a8a] text-white p-3 text-center border border-[#2e4a9a]">NAME</th>
                                        <th className="bg-[#1e3a8a] text-white p-3 text-center border border-[#2e4a9a]">STAFF ID</th>
                                        <th className="bg-[#1e3a8a] text-white p-3 text-center border border-[#2e4a9a]">DESIGNATION</th>
                                        <th className="bg-[#1e3a8a] text-white p-3 text-center border border-[#2e4a9a]">ROLE</th>
                                        <th className="bg-[#1e3a8a] text-white p-3 text-center border border-[#2e4a9a]">EMAIL</th>
                                        <th className="bg-[#1e3a8a] text-white p-3 text-center border border-[#2e4a9a]">EDIT</th>
                                        <th className="bg-[#1e3a8a] text-white p-3 text-center border border-[#2e4a9a]">DELETE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {/* {id, name, staff_id, role, created_at, updated_at, designation, email} */}
                                    {users.map((user) => (
                                        <tr key={user.id}>
                                            <td className="p-3 text-white border border-gray-700 text-center">{user.name}</td>
                                            <td className="p-3 text-white border border-gray-700 text-center">{user.staff_id}</td>
                                            <td className="p-3 text-white border border-gray-700 text-center">{user.designation}</td>
                                            <td className="p-3 text-white border border-gray-700 text-center">{user.role}</td>
                                            <td className="p-3 text-white border border-gray-700 text-center">{user.email}</td>
                                            <td className="p-3 text-white border border-gray-700 text-center">
                                                <button
                                                    className="text-blue-400 hover:text-blue-300"
                                                    onClick={() => handleEditModal(user)}
                                                >
                                                    EDIT
                                                </button>
                                            </td>
                                            <td className="p-3 text-white border border-gray-700 text-center">
                                                <button
                                                    className="text-red-400 hover:text-red-300"
                                                    onClick={() => handleDeleteModal(user)}
                                                >
                                                    DELETE
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

Users.layout = (page) => <Layout children={page} />;
export default Users;
