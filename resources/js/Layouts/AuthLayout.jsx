import Layout from "@/Layouts/Layout";
import { Head } from "@inertiajs/react";

export default function AuthLayout({ title, isUser, message, children }) {
    return (
        <Layout isDashboard={false} isUser={isUser}>
            <div className="flex grow">
                <div className="flex flex-col bg-gradient-to-r-base-300 w-[35vw] px-4 lg:pt-[5px] xl:pt-[50px] 2xl:pt-[80px] md:w-custom-width-1 xl:w-custom-width-1 2xl:w-custom-width-2">
                    <div>
                        <p className="font-rammetto md:text-[43px] xl:text-[43px] 2xl:text-[62px]">
                            WHERE FLIGHTS
                        </p>
                        <p className="font-rammetto md:text-[58px] xl:text-[58px] 2xl:text-[84px] text-TheBlue justify-start">
                            BEGIN
                            <img
                            className="absolute md:top-[130px] xl:top-[180px] 2xl:top-[235px] md:left-[190px] 2xl:left-[261px] overflow-visible max-w-none md:w-[11%] xl:w-[12%] 2xl:w-[12%]"
                            src="/img/sprtIc.svg"
                            height="auto"
                            alt="LHD"
                            />
                        </p>
                    </div>
                    <div className="relative grow xl:max-h-[450px] 2xl:max-h-[80px]">
                        <img
                            className="absolute md:top-7 xl:top-11 overflow-visible max-w-none md:w-[120%] xl:w-[123%] 2xl:w-[119%]"
                            src="/img/airplane.svg"
                            height="auto"
                            alt="LHD"
                        />
                    </div>
                </div>
                <div className="grow flex flex-col items-center pt-6 sm:justify-center sm:pt-0">
                    <div className="mt-6 w-full sm:max-w-md">
                        <Head title={title} />
                        <div className="pb-4">
                            <p className="text-3xl font-bold text-center uppercase">
                                {title}
                            </p>
                            <p className="text-center text-gray-400">
                                {message}
                            </p>
                        </div>
                        {children}
                    </div>
                </div>
            </div>
        </Layout>
    );
}
