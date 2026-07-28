"use client";

import Image from "next/image";
import Link from "next/link";
import { usePathname } from "next/navigation";

import {
  NAVIGATION_ITEMS,
  type NavigationItem,
} from "@/constants/navigation";

function isPathActive(pathname: string, item: NavigationItem): boolean {
  return item.activePaths.some((path) => {
    if (path === "/") {
      return pathname === "/";
    }

    return pathname === path || pathname.startsWith(`${path}/`);
  });
}

export function SiteHeader() {
  const pathname = usePathname();

  return (
    <header className="h-[70px] border-b border-neutral-200 bg-white">
      <div
        className="
          mx-auto
          flex
          h-full
          w-full
          max-w-[1440px]
          items-center
          px-5
          sm:px-[60px]
        "
      >
        <Link
          href="/"
          aria-label="Kembali ke halaman utama 9 Jobs"
          className="flex shrink-0 items-center"
        >
          <Image
            src="/assets/dcd-jobs-logo.svg"
            alt="9 Jobs"
            width={81}
            height={32}
            priority
          />
        </Link>

        <nav
          aria-label="Navigasi utama"
          className="
            ml-5
            flex
            h-full
            items-center
            gap-6
            sm:ml-8
            sm:gap-8
          "
        >
          {NAVIGATION_ITEMS.map((item) => {
            const isActive = isPathActive(pathname, item);

            return (
              <Link
                key={item.href}
                href={item.href}
                aria-current={isActive ? "page" : undefined}
                className="relative inline-flex h-8 items-center justify-center gap-1 px-2 text-base leading-6 font-normal tracking-[-0.011em] text-[#18181b]"
              >
                {item.label}

                {isActive ? (
                  <span
                    aria-hidden="true"
                    className="absolute -bottom-[3px] left-1/2 h-1 w-5 -translate-x-1/2 rounded-[1px] bg-[#18181b]"
                  />
                ) : null}
              </Link>
            );
          })}
        </nav>
      </div>
    </header>
  );
}
