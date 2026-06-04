import AppLogoIcon from './app-logo-icon';

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-8 items-center justify-center rounded-md bg-transparent">
                <AppLogoIcon className="h-full w-full object-contain" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <p className="leading-tight font-semibold">
                    PT.RIZQUNA BERKAH MANDIRI
                </p>
            </div>
        </>
    );
}
