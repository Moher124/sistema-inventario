<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

type Producto = { id_producto: number; nombre: string; precio_venta: string };

const props = defineProps<{
    productos: Producto[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Ventas', href: '/ventas' },
            { title: 'Nueva venta', href: '/ventas/crear' },
        ],
    },
});

const hoy = new Date().toISOString().slice(0, 10);

type Detalle = {
    id_producto: string;
    cantidad_vendida: number;
    precio_unitario: string;
};

const form = useForm({
    fecha_venta: hoy,
    observacion: '',
    tipo_cliente: 'CF' as 'CF' | 'NIT',
    nit_cliente: '',
    nombre_cliente: '',
    telefono_cliente: '',
    email_cliente: '',
    direccion_cliente: '',
    detalles: [
        { id_producto: '', cantidad_vendida: 1, precio_unitario: '' } as Detalle,
    ],
});

// --- Búsqueda de cliente por NIT ---
const clienteEncontrado = ref<boolean | null>(null); // null = aún no se buscó
const buscandoCliente = ref(false);
let debounceNit: ReturnType<typeof setTimeout> | null = null;

watch(
    () => form.nit_cliente,
    (nit) => {
        clienteEncontrado.value = null;
        form.nombre_cliente = '';
        form.telefono_cliente = '';
        form.email_cliente = '';
        form.direccion_cliente = '';

        if (debounceNit) clearTimeout(debounceNit);
        if (!nit) return;

        debounceNit = setTimeout(async () => {
            buscandoCliente.value = true;
            try {
                const response = await fetch(`/clientes/buscar-nit?nit=${encodeURIComponent(nit)}`);
                const data = await response.json();

                if (data.encontrado) {
                    clienteEncontrado.value = true;
                    form.nombre_cliente = data.cliente.nombre_cliente;
                    form.telefono_cliente = data.cliente.telefono_cliente ?? '';
                    form.email_cliente = data.cliente.email_cliente ?? '';
                    form.direccion_cliente = data.cliente.direccion_cliente ?? '';
                } else {
                    clienteEncontrado.value = false;
                }
            } finally {
                buscandoCliente.value = false;
            }
        }, 500);
    },
);

// --- Ítems de la venta ---
function agregarFila() {
    form.detalles.push({ id_producto: '', cantidad_vendida: 1, precio_unitario: '' });
}

function quitarFila(index: number) {
    if (form.detalles.length === 1) return;
    form.detalles.splice(index, 1);
}

function onProductoSeleccionado(index: number) {
    const productoId = Number(form.detalles[index].id_producto);
    const producto = props.productos.find((p) => p.id_producto === productoId);

    if (producto) {
        form.detalles[index].precio_unitario = producto.precio_venta;
    }
}


const totalVenta = computed(() =>
    form.detalles
        .reduce((suma, detalle) => {
            const cantidad = Number(detalle.cantidad_vendida) || 0;
            const precio = Number(detalle.precio_unitario) || 0;
            return suma + cantidad * precio;
        }, 0)
        .toFixed(2),
);

function submit() {
    form.post('/ventas');
}
</script>

<template>
    <Head title="Nueva venta" />

    <div class="flex flex-col gap-6 p-4 max-w-3xl">
        <h1 class="text-xl font-semibold">Nueva venta</h1>

        <form @submit.prevent="submit" class="flex flex-col gap-8">
            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="grid gap-2">
                    <Label for="fecha_venta">Fecha</Label>
                    <Input id="fecha_venta" type="date" v-model="form.fecha_venta" required />
                    <InputError :message="form.errors.fecha_venta" />
                </div>

                <div class="grid gap-2">
                    <Label for="tipo_cliente">Tipo de cliente</Label>
                    <select
                        id="tipo_cliente"
                        v-model="form.tipo_cliente"
                        class="rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                    >
                        <option value="CF">Consumidor Final</option>
                        <option value="NIT">Con NIT</option>
                    </select>
                    <InputError :message="form.errors.tipo_cliente" />
                </div>

                <template v-if="form.tipo_cliente === 'NIT'">
                    <div class="grid gap-2 sm:col-span-2">
                        <Label for="nit_cliente">NIT del cliente</Label>
                        <Input id="nit_cliente" v-model="form.nit_cliente" required />
                        <InputError :message="form.errors.nit_cliente" />
                        <p v-if="buscandoCliente" class="text-xs text-muted-foreground">Buscando...</p>
                        <p v-else-if="clienteEncontrado === true" class="text-xs text-green-600">
                            Cliente existente encontrado.
                        </p>
                        <p v-else-if="clienteEncontrado === false" class="text-xs text-amber-600">
                            Cliente nuevo — completá sus datos abajo.
                        </p>
                    </div>

                    <div class="grid gap-2 sm:col-span-2">
                        <Label for="nombre_cliente">Nombre</Label>
                        <Input
                            id="nombre_cliente"
                            v-model="form.nombre_cliente"
                            required
                            :readonly="clienteEncontrado === true"
                            :class="clienteEncontrado === true ? 'bg-muted' : ''"
                        />
                        <InputError :message="form.errors.nombre_cliente" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="telefono_cliente">Teléfono</Label>
                        <Input
                            id="telefono_cliente"
                            v-model="form.telefono_cliente"
                            :readonly="clienteEncontrado === true"
                            :class="clienteEncontrado === true ? 'bg-muted' : ''"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email_cliente">Correo</Label>
                        <Input
                            id="email_cliente"
                            type="email"
                            v-model="form.email_cliente"
                            :readonly="clienteEncontrado === true"
                            :class="clienteEncontrado === true ? 'bg-muted' : ''"
                        />
                    </div>
                </template>
            </section>

            <section class="flex flex-col gap-4 border-t border-sidebar-border/70 pt-6 dark:border-sidebar-border">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-medium text-muted-foreground uppercase tracking-wide">
                        Productos
                    </h2>
                    <Button type="button" variant="outline" size="sm" @click="agregarFila">
                        + Agregar producto
                    </Button>
                </div>

                <InputError :message="form.errors.detalles" />

                <div
                    v-for="(detalle, index) in form.detalles"
                    :key="index"
                    class="grid grid-cols-12 gap-2 items-start"
                >
                    <div class="col-span-5">
                        <select
                            v-model="detalle.id_producto"
                            required
                            class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            @change="onProductoSeleccionado(index)"
                        >
                            <option value="" disabled>Producto</option>
                            <option
                                v-for="producto in productos"
                                :key="producto.id_producto"
                                :value="producto.id_producto"
                            >
                                {{ producto.nombre }}
                            </option>
                        </select>
                    </div>

                    <div class="col-span-2">
                        <Input type="number" min="1" placeholder="Cant." v-model="detalle.cantidad_vendida" required />
                    </div>

                    <div class="col-span-3">
                        <Input type="number" step="0.01" min="0" placeholder="Precio" v-model="detalle.precio_unitario" required />
                    </div>

                    <div class="col-span-1 pt-2 text-right text-sm text-muted-foreground">
                        Q{{ ((Number(detalle.cantidad_vendida) || 0) * (Number(detalle.precio_unitario) || 0)).toFixed(2) }}
                    </div>

                    <div class="col-span-1 flex justify-end pt-1">
                        <button
                            type="button"
                            class="text-sm text-red-600 hover:underline"
                            :disabled="form.detalles.length === 1"
                            :class="form.detalles.length === 1 ? 'opacity-30 cursor-not-allowed' : ''"
                            @click="quitarFila(index)"
                        >
                            ✕
                        </button>
                    </div>
                </div>
            </section>

            <div class="grid gap-2">
                <Label for="observacion">Observación (opcional)</Label>
                <Input id="observacion" v-model="form.observacion" maxlength="255" />
                <InputError :message="form.errors.observacion" />
            </div>

            <div class="rounded-lg bg-muted/50 p-4 flex items-center justify-between">
                <span class="text-sm text-muted-foreground">Total de la venta</span>
                <span class="text-lg font-semibold">Q{{ totalVenta }}</span>
            </div>

            <div class="flex items-center gap-4">
                <Button type="submit" :disabled="form.processing">
                    <Spinner v-if="form.processing" />
                    Registrar venta
                </Button>
                <Link href="/ventas" class="text-sm text-muted-foreground hover:underline">
                    Cancelar
                </Link>
            </div>
        </form>
    </div>
</template>
