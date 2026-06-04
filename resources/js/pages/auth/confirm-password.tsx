import { Form, Head } from '@inertiajs/react';
import AuthImageSection from '@/components/auth-image-section';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';

export default function ConfirmPassword() {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center p-4">
            <Head title="Konfirmasi kata sandi" />

            <div className="grid w-full max-w-6xl items-center gap-4 rounded-md p-4 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.3)] max-md:max-w-lg max-md:gap-8 md:grid-cols-2">
                <div className="w-full px-4 py-4 md:max-w-md">
                    <div className="mb-12">
                        <h1 className="text-3xl font-bold text-slate-900">
                            Konfirmasi kata sandi
                        </h1>
                        <p className="mt-2 text-sm text-slate-600">
                            Ini adalah area aman dari aplikasi. Silakan
                            konfirmasi kata sandi Anda sebelum melanjutkan.
                        </p>
                    </div>

                    <Form {...store.form()} resetOnSuccess={['password']}>
                        {({ processing, errors }) => (
                            <>
                                <div>
                                    <Label
                                        htmlFor="password"
                                        className="mb-2 block text-[13px] font-medium text-slate-900"
                                    >
                                        Kata sandi
                                    </Label>
                                    <div className="relative flex items-center">
                                        <input
                                            id="password"
                                            name="password"
                                            type="password"
                                            placeholder="Masukkan kata sandi"
                                            autoComplete="current-password"
                                            autoFocus
                                            className="w-full border-b border-slate-300 py-3 pr-8 pl-2 text-sm text-slate-900 outline-none focus:border-blue-600"
                                        />
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="#bbb"
                                            stroke="#bbb"
                                            className="absolute right-2 h-4.5 w-4.5 cursor-pointer"
                                            viewBox="0 0 128 128"
                                        >
                                            <path
                                                d="M64 104C22.127 104 1.367 67.496.504 65.943a4 4 0 0 1 0-3.887C1.367 60.504 22.127 24 64 24s62.633 36.504 63.496 38.057a4 4 0 0 1 0 3.887C126.633 67.496 105.873 104 64 104zM8.707 63.994C13.465 71.205 32.146 96 64 96c31.955 0 50.553-24.775 55.293-31.994C114.535 56.795 95.854 32 64 32 32.045 32 13.447 56.775 8.707 63.994zM64 88c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z"
                                                data-original="#000000"
                                            ></path>
                                        </svg>
                                    </div>
                                    <InputError message={errors.password} />
                                </div>

                                <div className="mt-12">
                                    <Button
                                        type="submit"
                                        className="w-full rounded-md bg-blue-600 px-4 py-2.5 text-sm font-medium tracking-wide text-white shadow-xl hover:bg-blue-700 focus:outline-none"
                                        disabled={processing}
                                        data-test="confirm-password-button"
                                    >
                                        {processing && (
                                            <Spinner className="mr-2" />
                                        )}
                                        Konfirmasi
                                    </Button>
                                </div>
                            </>
                        )}
                    </Form>
                </div>

                <AuthImageSection alt="confirm-password-image" />
            </div>
        </div>
    );
}
