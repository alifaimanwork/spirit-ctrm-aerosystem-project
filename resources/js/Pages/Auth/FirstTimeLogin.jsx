import { Link, useForm } from "@inertiajs/react";

import AuthLayout from "@/Layouts/AuthLayout";

function FirstTimeLogin() {
    const { data, setData, post, processing, errors, reset } = useForm({
        staff_id: "",
        password: "",
        password_confirmation: "",
    });

    const submit = (e) => {
        e.preventDefault();

        post(route("first-time-login"), {
            onFinish: () => reset("password", "password_confirmation"),
        });
    };

    return (
        <form onSubmit={submit}>
            <label className="form-control w-full">
                <div className="label">
                    <span className="label-text text-xl">STAFF ID</span>
                </div>
                <input
                    id="staff_id"
                    type="text"
                    name="staff_id"
                    value={data.staff_id}
                    className="input-btm-border w-full"
                    autoComplete="username"
                    onChange={(e) => setData("staff_id", e.target.value)}
                />
                <div className="label">
                    <span className="label-text text-error">
                        {errors.staff_id}
                    </span>
                </div>
            </label>
            <label className="form-control w-full">
                <div className="label">
                    <span className="label-text text-xl">PASSWORD</span>
                </div>
                <input
                    id="password"
                    type="password"
                    name="password"
                    value={data.password}
                    className="input-btm-border w-full"
                    autoComplete="new-password"
                    onChange={(e) => setData("password", e.target.value)}
                    required
                />
                <div className="label">
                    <span className="label-text text-error">
                        {errors.password}
                    </span>
                </div>
            </label>

            <label className="form-control w-full">
                <div className="label">
                    <span className="label-text text-xl">CONFIRM PASSWORD</span>
                </div>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    value={data.password_confirmation}
                    className="input-btm-border w-full"
                    autoComplete="new-password"
                    onChange={(e) =>
                        setData("password_confirmation", e.target.value)
                    }
                    required
                />
                <div className="label">
                    <span className="label-text text-erro">
                        {errors.password_confirmation}
                    </span>
                </div>
            </label>

            <div className="mt-4 flex flex-row-reverse">
                <Link href={route("login")} className=" link link-primary ">
                    Already had Login?
                </Link>
            </div>

            <div className="mt-4 flex justify-center">
                <button
                    className="btn btn-primary btn-sm px-12"
                    disabled={processing}
                >
                    Create Password
                </button>
            </div>
        </form>
    );
}

FirstTimeLogin.layout = (page) => (
    <AuthLayout
        children={page}
        title="First Time Login"
        message="Create password using Staff ID here."
    />
);
export default FirstTimeLogin;
