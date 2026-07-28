import type { ComponentPropsWithoutRef } from "react";

type CardProps = ComponentPropsWithoutRef<"article">;

export function Card({ className = "", children, ...props }: CardProps) {
  return (
    <article
      className={`
        rounded-lg
        border
        border-neutral-200
        bg-white
        ${className}
      `}
      {...props}
    >
      {children}
    </article>
  );
}
