import Image from "next/image";

const COMPANY_ADDRESS = [
  "Dicoding Space",
  "Jl. Batik Kumeli No.50, Sukaluyu,",
  "Kec. Cibeunying Kaler, Kota Bandung Jawa",
  "Barat 40123",
];

export function SiteFooter() {
  return (
    <footer className="h-auto min-h-[328px] bg-white px-5 sm:px-[60px]">
      <div className="mx-auto h-full w-full max-w-[1110px] border-t border-neutral-200">
        <div className="pt-16">
          <Image
            src="/assets/dcd-logo.svg"
            alt="Dicoding"
            width={138}
            height={34}
          />

          <address className="mt-8 max-w-[378px] text-base leading-6 font-normal tracking-[-0.011em] text-neutral-500 not-italic">
            {COMPANY_ADDRESS.map((line) => (
              <span key={line} className="block">
                {line}
              </span>
            ))}
          </address>
        </div>
      </div>
    </footer>
  );
}
