"use client";

import { ReactNode, useState } from "react";
import { Context } from "./context";

// Provider
type ProviderProps = {
    children: ReactNode;
};

export default function Provider(props: ProviderProps) {
    const { children } = props;

    const [isOpen, setIsOpen] = useState(false);

    // Objet valeur à fournir aux enfants
    const value = { isOpen, setIsOpen };

    return <Context.Provider value={value}>{children}</Context.Provider>;
}
