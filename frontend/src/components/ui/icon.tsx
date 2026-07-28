import type { ReactNode, SVGProps } from "react";

export type IconName =
  | "briefcase"
  | "clock"
  | "edit"
  | "plus"
  | "trash"
  | "upload";

interface IconProps extends SVGProps<SVGSVGElement> {
  name: IconName;
}

const iconPaths: Record<IconName, ReactNode> = {
  briefcase: (
    <>
      <path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7" />
      <rect x="3.5" y="7" width="17" height="13" rx="1" />
      <path d="M3.5 11.5h17M10" />
    </>
  ),
  clock: (
    <>
      <circle cx="12" cy="12" r="8.5" />
      <path d="M12 7.5V12l3 2" />
    </>
  ),
  edit: (
    <>
      <path d="m4 20 4-1 10.5-10.5a1.5 1.5 0 0 0 0-2.1l-.9-.9a1.5 1.5 0 0 0-2.1 0L5 16l-1 4Z" />
      <path d="m14.5 6.5 3 3" />
    </>
  ),
  plus: <path d="M12 5v14M5 12h14" />,
  trash: (
    <>
      <path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13M10 11v5M14 11v5" />
    </>
  ),
  upload: (
    <>
      <path d="M12 16V4M7.5 8.5 12 4l4.5 4.5" />
      <path d="M5 14v5h14v-5" />
    </>
  ),
};

export function Icon({ name, ...props }: IconProps) {
  return (
    <svg
      aria-hidden="true"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="1.8"
      strokeLinecap="round"
      strokeLinejoin="round"
      {...props}
    >
      {iconPaths[name]}
    </svg>
  );
}
