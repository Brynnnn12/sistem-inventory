interface AuthImageSectionProps {
    alt: string;
}

export default function AuthImageSection({ alt }: AuthImageSectionProps) {
    return (
        <div className="flex hidden h-full w-full items-center rounded-xl bg-[#000842] p-8 md:block">
            <img
                src="/images/login.jpg"
                className="h-full w-full rounded-lg object-cover"
                alt={alt}
            />
        </div>
    );
}
