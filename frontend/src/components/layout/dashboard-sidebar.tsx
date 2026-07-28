import Image from "next/image";
import Link from "next/link";

import { Icon } from "@/components/ui/icon";

export function DashboardSidebar() {
  return (
    <aside className="w-full shrink-0 border-neutral-200 bg-white md:w-[286px] md:border-r">
      <div className="hidden h-[101px] items-center justify-between overflow-hidden pl-5 pr-10 md:flex">
        <h2 className="text-2xl font-medium text-neutral-700">Jobs</h2>

        <Image
          src="/assets/line-job.svg"
          alt=""
          width={84}
          height={92}
          aria-hidden="true"
          className="shrink-0 self-start"
        />
      </div>

      <nav aria-label="Navigasi dashboard" className="p-4 md:p-0">
        <Link
          href="/dashboard"
          aria-current="page"
          className="flex min-h-[62px] items-center gap-3 bg-neutral-100 px-5 text-base font-normal text-neutral-800"
        >
          <Icon name="briefcase" className="size-5" />
          Lowongan Saya
        </Link>
      </nav>
    </aside>
  );
}
