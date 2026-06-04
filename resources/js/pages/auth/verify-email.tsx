// Components
import { Form, Head } from '@inertiajs/react';
import AuthImageSection from '@/components/auth-image-section';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

export default function VerifyEmail({ status }: { status?: string }) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center p-4">
            <Head title="Verifikasi email" />

            <div className="grid w-full max-w-6xl items-center gap-4 rounded-md p-4 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.3)] max-md:max-w-lg max-md:gap-8 md:grid-cols-2">
                <div className="w-full px-4 py-4 md:max-w-md">
                    <div className="mb-12">
                        <h1 className="text-3xl font-bold text-slate-900">
                            Verifikasi email
                        </h1>
                        <p className="mt-2 text-sm text-slate-600">
                            Silakan verifikasi alamat email Anda dengan mengklik
                            tautan yang baru saja kami kirimkan kepada Anda.
                        </p>
                    </div>

                    {status === 'verification-link-sent' && (
                        <div className="mt-4 text-center text-sm font-medium text-green-600">
                            Tautan verifikasi baru telah dikirim ke alamat email
                            yang Anda berikan saat pendaftaran.
                        </div>
                    )}

                    <Form {...send.form()} className="space-y-6 text-center">
                        {({ processing }) => (
                            <>
                                <Button
                                    disabled={processing}
                                    variant="secondary"
                                    className="w-full rounded-md bg-blue-600 px-4 py-2.5 text-sm font-medium tracking-wide text-white shadow-xl hover:bg-blue-700 focus:outline-none"
                                >
                                    {processing && <Spinner className="mr-2" />}
                                    Kirim ulang email verifikasi
                                </Button>

                                <TextLink
                                    href={logout()}
                                    className="mx-auto block text-sm text-slate-900 hover:underline"
                                >
                                    Keluar
                                </TextLink>
                            </>
                        )}
                    </Form>
                </div>

                <AuthImageSection alt="verify-email-image" />
            </div>
        </div>
    );
}
