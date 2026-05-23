<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const plan = ref('pro')
const amount = ref(10000)
const extended = ref(false)

const loading = ref(false)
const result = ref(null)
const error = ref(null)

const simulate = async () => {

  loading.value = true
  error.value = null

  try {

    const response = await axios.get('/api/simulate', {
      params: {
        plan: plan.value,
        amount: amount.value,
        mode: extended.value ? 'extended' : null
      }
    })

    result.value = response.data

  } catch (e) {

    error.value =
      'Unable to fetch live market price.'

  } finally {

    loading.value = false
  }
}

watch([plan, amount, extended], simulate, {
  immediate: true
})
</script>

<template>

<div
    class="min-h-screen relative overflow-hidden
           bg-[#0B1120] text-white"
>

    <!-- BACKGROUND GLOWS -->
    <div
        class="absolute top-0 left-0 w-[500px] h-[500px]
               bg-blue-500/20 blur-3xl rounded-full"
    ></div>

    <div
        class="absolute bottom-0 right-0 w-[500px] h-[500px]
               bg-purple-500/20 blur-3xl rounded-full"
    ></div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 py-10">

        <!-- HERO -->
        <div class="text-center mb-12">

            <div
                class="inline-flex items-center gap-3
                       bg-white/10 backdrop-blur-lg
                       border border-white/10
                       px-5 py-2 rounded-full mb-6"
            >
                <div
                    class="w-2 h-2 bg-green-400
                           rounded-full animate-pulse"
                ></div>

                <span class="text-sm text-gray-300">
                    LIVE HTX MARKET SIMULATION
                </span>
            </div>

            <h1
                class="text-5xl md:text-6xl font-black
                       tracking-tight"
            >
                Zero Fee
                <span
                    class="bg-gradient-to-r
                           from-blue-400 to-cyan-300
                           bg-clip-text text-transparent"
                >
                    Crypto Trading
                </span>
            </h1>

            <p
                class="text-gray-400 max-w-2xl mx-auto
                       mt-5 text-lg"
            >
                Simulate HTX broker rebates, platform execution,
                and zero-fee trading economics in real time.
            </p>
        </div>

        <!-- MAIN PANEL -->
        <div
            class="bg-white/5 backdrop-blur-2xl
                   border border-white/10
                   rounded-[32px] shadow-2xl
                   p-6 md:p-10"
        >

            <!-- FORM -->
            <div
                class="grid grid-cols-1 lg:grid-cols-3 gap-6"
            >

                <!-- PLAN -->
                <div>

                    <label
                        class="block text-sm text-gray-400 mb-3"
                    >
                        Trading Plan
                    </label>

                    <select
                        v-model="plan"
                        :disabled="loading"
                        class="w-full bg-white/10
                               border border-white/10
                               rounded-2xl p-4
                               text-white
                               focus:ring-2
                               focus:ring-cyan-400"
                    >
                        <option
                            value="free"
                            class="text-black"
                        >
                            Free Starter
                        </option>

                        <option
                            value="pro"
                            class="text-black"
                        >
                            Pro ₹199/month
                        </option>
                    </select>
                </div>

                <!-- AMOUNT -->
                <div>

                    <label
                        class="block text-sm text-gray-400 mb-3"
                    >
                        Purchase Amount
                    </label>

                    <input
                        v-model.number="amount"
                        type="number"
                        min="100"
                        max="5000000"
                        placeholder="Enter INR amount"
                        :disabled="loading"

                        class="w-full bg-white/10
                               border border-white/10
                               rounded-2xl p-4
                               text-white placeholder-gray-500
                               focus:ring-2
                               focus:ring-cyan-400"
                    />

                    <p
                        v-if="amountError"
                        class="text-red-400 text-sm mt-2"
                    >
                        {{ amountError }}
                    </p>
                </div>

                <!-- TOGGLE -->
                <div>

                    <label
                        class="block text-sm text-gray-400 mb-3"
                    >
                        Simulation Mode
                    </label>

                    <button
                        @click="extended = !extended"
                        :disabled="loading"

                        class="w-full p-4 rounded-2xl
                               font-bold transition-all
                               duration-300 border"

                        :class="extended
                            ? 'bg-gradient-to-r from-cyan-500 to-blue-600 border-cyan-400'
                            : 'bg-white/10 border-white/10 hover:bg-white/20'"
                    >
                        {{
                            extended
                                ? 'Extended Calculation Enabled'
                                : 'Core Calculation'
                        }}
                    </button>
                </div>

            </div>

            <!-- BUTTON -->
            <div class="mt-8">

                <button
                    @click="simulate"
                    :disabled="loading || !isValid"

                    class="group relative overflow-hidden
                           px-8 py-4 rounded-2xl
                           font-bold text-lg
                           bg-gradient-to-r
                           from-cyan-500 to-blue-600
                           hover:scale-[1.02]
                           transition-all duration-300
                           disabled:opacity-50"
                >

                    <span class="relative z-10">

                        <span v-if="loading">
                            Processing Simulation...
                        </span>

                        <span v-else>
                            Simulate Trade
                        </span>

                    </span>

                    <div
                        class="absolute inset-0
                               bg-white/20 opacity-0
                               group-hover:opacity-100
                               transition"
                    ></div>

                </button>
            </div>

        </div>

        <!-- LOADING -->
        <div
            v-if="loading"
            class="mt-10 bg-white/5 backdrop-blur-xl
                   border border-white/10
                   rounded-[28px] p-10 text-center"
        >

            <div
                class="w-20 h-20 mx-auto
                       border-[5px]
                       border-cyan-400/30
                       border-t-cyan-400
                       rounded-full animate-spin"
            ></div>

            <h3 class="text-2xl font-bold mt-6">
                Fetching Live Market Data
            </h3>

            <p class="text-gray-400 mt-2">
                Connecting to HTX exchange...
            </p>
        </div>

        <!-- ERROR -->
        <div
            v-if="error"
            class="mt-8 bg-red-500/10
                   border border-red-500/20
                   rounded-3xl p-6"
        >
            <h3 class="text-red-400 font-bold text-lg">
                API Error
            </h3>

            <p class="text-red-300 mt-2">
                {{ error }}
            </p>
        </div>

        <!-- RESULTS -->
        <div
            v-if="result && !loading"
            class="mt-10 space-y-8"
        >

            <!-- CORE -->
            <div
                class="grid grid-cols-1 md:grid-cols-5 gap-5"
            >

                <div
                    v-for="card in [
                        {
                            title: 'HTX Fee',
                            value: '₹' + result.htx_fee_inr
                        },
                        {
                            title: 'Broker Rebate',
                            value: '₹' + result.rebate_inr
                        },
                        {
                            title: 'Net Platform Cost',
                            value: '₹' + result.net_cost_to_platform
                        },
                        {
                            title: 'Customer Pays',
                            value: '₹' + result.customer_pays_fee
                        },
                        {
                            title: 'BTC Received',
                            value: result.final_crypto_received
                        }
                    ]"

                    class="bg-white/5
                           border border-white/10
                           rounded-3xl p-5
                           hover:bg-white/10
                           transition-all duration-300"
                >

                    <p class="text-gray-400 text-sm">
                        {{ card.title }}
                    </p>

                    <h3
                        class="text-2xl font-black mt-3
                               break-all"
                    >
                        {{ card.value }}
                    </h3>

                </div>

            </div>

            <!-- EXTENDED -->
            <div
                v-if="result.extended_mode"
                class="grid grid-cols-1 xl:grid-cols-2 gap-8"
            >

                <!-- USER VIEW -->
                <div
                    class="bg-gradient-to-br
                           from-cyan-500 to-blue-700
                           rounded-[32px] p-8 shadow-2xl"
                >

                    <div
                        class="flex items-center justify-between mb-8"
                    >

                        <div>

                            <h2 class="text-3xl font-black">
                                User View
                            </h2>

                            <p class="text-cyan-100 mt-2">
                                What the customer sees
                            </p>
                        </div>

                        <div class="text-5xl">
                            👤
                        </div>
                    </div>

                    <div class="space-y-5">

                        <div
                            class="bg-white/10 backdrop-blur-lg
                                   rounded-3xl p-6"
                        >
                            <p class="text-cyan-100">
                                Displayed Buy Price
                            </p>

                            <h3
                                class="text-4xl font-black mt-3"
                            >
                                ₹{{ result.displayed_price_inr }}
                            </h3>
                        </div>

                        <div
                            class="grid grid-cols-2 gap-5"
                        >

                            <div
                                class="bg-white/10
                                       rounded-3xl p-5"
                            >
                                <p class="text-cyan-100 text-sm">
                                    INR Amount
                                </p>

                                <h3
                                    class="text-2xl font-bold mt-3"
                                >
                                    ₹{{ result.amount_inr }}
                                </h3>
                            </div>

                            <div
                                class="bg-white/10
                                       rounded-3xl p-5"
                            >
                                <p class="text-cyan-100 text-sm">
                                    Expected BTC
                                </p>

                                <h3
                                    class="text-lg font-bold mt-3 break-all"
                                >
                                    {{ result.expected_crypto_received }}
                                </h3>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- EXECUTION -->
                <div
                    class="bg-white/5 backdrop-blur-xl
                           border border-white/10
                           rounded-[32px] p-8"
                >

                    <div
                        class="flex items-center justify-between mb-8"
                    >

                        <div>

                            <h2
                                class="text-3xl font-black"
                            >
                                Platform Execution
                            </h2>

                            <p class="text-gray-400 mt-2">
                                Internal processing logic
                            </p>
                        </div>

                        <div class="text-5xl">
                            ⚡
                        </div>
                    </div>

                    <div class="space-y-5">

                        <div
                            class="bg-white/5 rounded-3xl p-5"
                        >
                            <p class="text-gray-400">
                                Executable Amount
                            </p>

                            <h3
                                class="text-3xl font-black mt-3"
                            >
                                ₹{{ result.executable_amount }}
                            </h3>
                        </div>

                        <div
                            class="bg-white/5 rounded-3xl p-5"
                        >
                            <p class="text-gray-400">
                                True HTX Market Price
                            </p>

                            <h3
                                class="text-3xl font-black mt-3"
                            >
                                ₹{{ result.market_price_inr }}
                            </h3>
                        </div>

                        <div
                            class="bg-white/5 rounded-3xl p-5"
                        >
                            <p class="text-gray-400">
                                Actual Crypto Executed
                            </p>

                            <h3
                                class="text-xl font-black mt-3 break-all"
                            >
                                {{ result.final_crypto_received }}
                            </h3>
                        </div>

                        <div
                            class="bg-gradient-to-r
                                   from-green-500 to-emerald-600
                                   rounded-3xl p-6"
                        >
                            <p class="text-green-100">
                                Captured Revenue
                            </p>

                            <h3
                                class="text-5xl font-black mt-3"
                            >
                                ₹{{ result.captured_spread_revenue_inr }}
                            </h3>

                            <p class="text-green-100 mt-2">
                                Spread + execution adjustment
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


  

</div>

</template>