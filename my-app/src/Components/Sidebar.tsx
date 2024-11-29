// components/Sidebar.tsx
"use client";
import { SERVER_URL } from "@/lib/constant";
import { useRouter } from "next/navigation";
import React, { ChangeEvent, FormEvent, useState } from "react";

interface SidebarProps {
  show: boolean;
  plan: string;
  cost: string;
  onClose: () => void;
}

const Sidebar: React.FC<SidebarProps> = ({ show, plan, cost, onClose }) => {
  const router = useRouter();
  const [paymentDetails, setPaymentDetails] = useState({
    email: "",
    password: "",
    card_number: "",
    expiration_date: "",
    cvv: "",
  });
  const [error, setError] = useState("");

  const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setPaymentDetails((prev) => ({ ...prev, [name]: value }));
  };

  const onSubmit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    try {
      console.log(paymentDetails);
      const res = await fetch(`${SERVER_URL}/pricing.php`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        credentials: "include",
        body: JSON.stringify({
          ...paymentDetails,
          plan_name: plan,
        }),
      });

      const data = await res.json();
      if (data.status === "error") {
        setError(data.message || "An error occurred during login.");
        return;
      }

      router.push("/generatecaption");
    } catch (err) {
      console.error("Login failed:", err);
      setError("An unexpected error occurred.");
    }
  };
  return (
    <div
      className={`fixed inset-0 z-50 transition-transform transform ${
        show ? "translate-x-0" : "translate-x-full"
      } bg-gray-800 bg-opacity-50`}
    >
      <div className="absolute right-0 w-80 h-full bg-white shadow-lg p-4">
        <button onClick={onClose} className="text-gray-500 hover:text-gray-700">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            className="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
        <h2 className="text-2xl font-bold text-gray-800 mb-4">
          Payment Details
        </h2>
        <p className="text-gray-800 font-semibold mb-2">Plan: {plan}</p>
        <p className="text-gray-800 font-semibold mb-4">
          Cost: ${cost} / month
        </p>
        <form className="mt-4 space-y-4" onSubmit={onSubmit}>
          <label htmlFor="email" className="block text-gray-800 font-medium">
            Email
            <input
              type="email"
              id="email"
              name="email"
              onChange={handleChange}
              className="mt-1 p-2 block w-full rounded-xl bg-white  border-teal-700 border-2  text-black"
              required
            />
          </label>

          <label htmlFor="password" className="block text-gray-800 font-medium">
            Password
            <input
              type="password"
              id="password"
              name="password"
              onChange={handleChange}
              className="mt-1 p-2 block w-full rounded-xl bg-white  border-teal-700 border-2 text-black "
              required
            />
          </label>

          <label
            htmlFor="cardnumber"
            className="block text-gray-800 font-medium"
          >
            Card Number
            <input
              type="text"
              id="cardnumber"
              name="card_number"
              onChange={handleChange}
              className="mt-1 p-2 block w-full rounded-xl bg-white  border-teal-700 border-2 text-black "
              required
            />
          </label>

          <label
            htmlFor="expirationdate"
            className="block text-gray-800 font-medium"
          >
            Expiration Date
            <input
              type="text"
              id="expirationdate"
              name="expiration_date"
              onChange={handleChange}
              className="mt-1 p-2 block w-full rounded-xl bg-white  border-teal-700 border-2 text-black"
              required
            />
          </label>

          <label htmlFor="cvv" className="block text-gray-800 font-medium">
            CVV
            <input
              type="text"
              id="cvv"
              name="cvv"
              onChange={handleChange}
              className="mt-1 p-2 block w-full rounded-xl bg-white  border-teal-700 border-2 text-black mb-6"
              required
            />
          </label>
          <p className="text-center text-red-500">{error}</p>
          <button
            type="submit"
            className="bg-teal-700 text-white py-2 px-4 rounded-full w-full"
          >
            Pay Now
          </button>
        </form>
      </div>
    </div>
  );
};

export default Sidebar;
