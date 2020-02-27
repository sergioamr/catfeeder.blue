import os

import numpy
import imageio

import random

from skimage import data, color
from skimage.transform import rescale, resize, downscale_local_mean

data_folder = "/classifier"
xs = []
ys = []

full_path = os.getcwd() + data_folder

labels = []
labels_name = []
file_paths = []

batch_size = 250

for directories in os.listdir(full_path):
    dir = os.path.join(full_path, directories)
    onlyfiles = [f for f in os.listdir(dir) if os.path.isfile(os.path.join(dir, f))]

    batch_set = len(labels_name)
    print("--- %s ---" % directories)
    for i in range(len(onlyfiles)):
        path_file = dir + "/" + onlyfiles[i]
        print(" %i: %s " %(i, path_file))
        labels.append(batch_set)
        file_paths.append(path_file)

    if (len(onlyfiles) > 0):
        labels_name.append(directories)

print("### LABELS ###")
for i in range(len(labels_name)):
    print(" %i: %s " %(i, labels_name[i]))

print("### TOTAL FILES  ###")
print(" %i" %(len(file_paths)))

output_path = os.getcwd() + "/output/"

######## Randomize set #########
combined = list(zip(labels, file_paths))
random.shuffle(combined)
labels[:], file_paths[:] = zip(*combined)

########## Save labels #########
# dump the labels

L = 'var labels=' + `list(labels[:])` + ';\n'
open(output_path + 'fr_labels.js', 'w').write(L)

########## Create batch #########

xs = []
print xs

batches_n = len(file_paths) / batch_size

print("------ NUM BATCHES %i ------" % batches_n)

b = 0
for i in range(len(file_paths)):
#for i in range(3):

    im = imageio.imread(file_paths[i])
    im_resized = image_rescaled = resize(im, (32, 32, 3), mode='reflect')
    rescaled_image = 255 * im_resized
    im_resized = rescaled_image.astype(numpy.uint8)

    xarr = im_resized[:, :, :]
    imageio.imwrite(output_path + "%i.png" %i, xarr)

    if i == 0:
        xs = xarr

    if (i % 50 == 0):
        print("%i : -----------" %i)
        print xs.shape

    if (i != 0 and i % (batch_size) == 0):
        print("+ %i/%i ::: Batch ::: " % (i, batches_n))

        xv = xs[:((1024*batch_size)/32), :, :]
        print xv.shape

        x = xv.reshape(batch_size,1024,3)
        imageio.imwrite(output_path + "output_batch_%i.png" % b, x)

        b = int(i / batch_size)
        xs = xarr
    else:
        xs = numpy.concatenate((xs, xarr))

