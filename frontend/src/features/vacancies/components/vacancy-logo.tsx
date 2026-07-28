import Image from "next/image";

interface VacancyLogoProps {
  src?: string | null;
  companyName: string;
  size?: "compact" | "default";
}

export function VacancyLogo({
  src,
  companyName,
  size = "default",
}: VacancyLogoProps) {
  const sizeClass = size === "compact" ? "size-16" : "size-[102px]";
  const imageSize = size === "compact" ? "64px" : "102px";

  return (
    <div
      className={`relative flex shrink-0 items-center justify-center overflow-hidden rounded border border-neutral-200 bg-white ${sizeClass}`}
    >
      {src ? (
        <Image
          src={src}
          alt={`Logo ${companyName}`}
          fill
          sizes={imageSize}
          className="object-contain"
        />
      ) : (
        <span className="px-2 text-center text-xs text-neutral-500">
          {companyName}
        </span>
      )}
    </div>
  );
}
