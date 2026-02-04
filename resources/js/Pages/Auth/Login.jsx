import { Link, useForm } from "@inertiajs/react";

import AuthLayout from "@/Layouts/AuthLayout";

function Login() {
    const { data, setData, post, processing, errors, reset } = useForm({
        staff_id: "",
        password: "",
        remember: true,
    });

    const submit = (e) => {
        e.preventDefault();

        post(route("login"), {
            onFinish: () => reset("password"),
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
                    autoComplete="password"
                    onChange={(e) => setData("password", e.target.value)}
                    required
                />
                <div className="label">
                    <span className="label-text text-error">
                        {errors.password}
                    </span>
                </div>
            </label>
            <div className="mt-4 flex flex-row-reverse">
                <Link
                    href={route("first-time-login")}
                    className="link link-primary"
                >
                    First Time Login?
                </Link>
            </div>

            <div className="mt-4 flex justify-center">
                <button
                    className="btn btn-primary btn-sm px-12"
                    disabled={processing}
                >
                    LOG IN
                </button>
            </div>
        </form>
    );
}

Login.layout = (page) => <AuthLayout children={page} title="Login" />;

export default Login;
