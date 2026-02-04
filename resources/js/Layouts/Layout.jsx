import { useRef, useEffect, useState } from "react";
import { Link, usePage } from "@inertiajs/react";
import { Home06Icon, Menu01Icon } from "hugeicons-react";

import { ROLES } from "@/enums/roles";

function MenuDropdown({ isAdmin }) {
    const menu = useRef(null);

    function handleSuccess() {
        document.activeElement.blur();
    }
    return (
        <div className="dropdown dropdown-end p-1">
            <div ref={menu} tabIndex="0" id="1" className="btn-icon">
                <Menu01Icon className="xl:size-6" />
            </div>
            <ul
                tabIndex="0"
                className="menu menu-compact dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-52 z-50"
            >
                <li>
                    <Link href={route("dashboard")} method="get" as="button">
                        PRODUCTION LIST
                    </Link>
                </li>
                <div className="divider mt-0 mb-0"></div>
                <li>
                    <Link href={route("liveproduction")} method="get" as="button">
                        LIVE PRODUCTION
                    </Link>
                </li>
                {/* <div className="divider mt-0 mb-0"></div>
                <li>
                    <Link href={route("resultlog")} method="get" as="button">
                        RESULT LOG
                    </Link>
                </li> */}
                <div className="divider mt-0 mb-0"></div>
                <li>
                    <Link href={route("production.report")} method="get" as="button">
                        PRODUCTION REPORT
                    </Link>
                </li>
                {/* <div className="divider mt-0 mb-0"></div>
                <li className="justify-between">
                    <p>EDIT ACCOUNT</p>
                </li> */}
                {isAdmin && (
                    <>
                        <div className="divider mt-0 mb-0"></div>
                        <li className="">
                            <Link
                                href={route("users")}
                                as="button"
                                onSuccess={handleSuccess}
                            >
                                USER LIST
                            </Link>
                        </li>
                    </>
                )}
                <div className="divider mt-0 mb-0"></div>
                <li>
                    <Link href={route("logout")} method="post" as="button">
                        LOGOUT
                    </Link>
                </li>
            </ul>
        </div>
    );
}

export default function Layout({ isDashboard, children }) {
    const { auth } = usePage().props;

    return (
        <div className="min-h-screen flex flex-col relative">
            <div className="grid grid-cols-3 justify-between p-2 items-center bg-gradient-to-r-base-200">
                <div className="flex space-x-2 items-center">
                    <img
                        className="object-contain md:h-6 xl:h-8"
                        src="/img/CTRM.png"
                        alt="logo"
                    />
                    {isDashboard ?? (
                        <Link
                            className="btn-icon"
                            href={route("dashboard")}
                            as="button"
                        >
                            <Home06Icon className="xl:size-7" />
                        </Link>
                    )}
                </div>
                <div className="flex justify-center">
                    <p className="md:text-sm xl:text-lg">
                        DIGITAL VISION INSPECTION SYSTEM
                    </p>
                </div>

                <div className="justify-self-end">
                    <div className="flex">
                        {isDashboard ?? (
                            <MenuDropdown
                                isAdmin={
                                    auth.user.role == ROLES.superadmin ||
                                    auth.user.role == ROLES.admin
                                }
                            />
                        )}
                    </div>
                </div>
            </div>
            {children}
        </div>
    );
}
